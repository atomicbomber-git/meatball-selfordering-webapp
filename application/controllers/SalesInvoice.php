<?php

use App\BaseController;
use Illuminate\Database\Capsule\Manager as DB;
use App\EloquentModels\SalesInvoice as SalesInvoiceModel;
use App\Helpers\Auth;
use Illuminate\Support\Carbon;
use App\EloquentModels\PlannedSalesInvoiceItem;
use App\EloquentModels\SalesInvoiceItem;
use App\Policies\SalesInvoicePolicy;
use App\EloquentModels\OutletMenuItem;
use App\Helpers\Formatter;
use Mike42\Escpos\Printer;

class SalesInvoice extends BaseController {

    const RECEIPT_COLUMN_01_LENGTH = 29;
    const RECEIPT_COLUMN_02_LENGTH = 11;
    const RECEIPT_SEPARATOR_LENGTH = 40;

    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'create' => ['get'],
            'confirm' => ['get'],
            'processConfirm' => ['post'],
            'updateAndConfirm' => ['get'],
            'processUpdateAndConfirm' => ['post'],
            'store' => ['post'],
            'edit' => ['get'],
            'update' => ['post'],
            'delete' => ['post'],
        ];
    }

    private $sales_invoice;

    public function index()
    {
        SalesInvoicePolicy::canIndex(Auth::user()) ?: $this->error403();

        $sales_invoices = SalesInvoiceModel::query()
            ->whereDate("created_at", Carbon::today())
            ->where("status", SalesInvoiceModel::UNPAID)
            ->get();

        $this->template->render("sales_invoice/index", compact("sales_invoices"));
    }

    public function confirm($sales_invoice_id)
    {
        SalesInvoicePolicy::canConfirm(Auth::user()) ?: $this->error403();

        $outlet = Auth::user()->outlet ?: $this->error403();

        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $sales_invoice->load([
            "outlet",
            "outlet.cashier_printer",
            "outlet.kitchen_printer",
            "planned_sales_invoice_items",
            "planned_sales_invoice_items.menu_item",
            "planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) use($outlet) {
                $query->where("outlet_id", $outlet->id);
            }
        ]);

        $sales_invoice->sorted_planned_sales_invoice_items = $sales_invoice->planned_sales_invoice_items
            ->sortBy(function ($psi_item) {
                return $psi_item->menu_item->name;
            })
            ->values()
            ->all();
        
        $this->template->render("sales_invoice/confirm", compact("sales_invoice"));
    }

    public function processConfirm($sales_invoice_id)
    {
        $this->validate([
            ["cash", "cash", "required|greater_than[0]"],
        ]);

        SalesInvoicePolicy::canConfirm(Auth::user()) ?: $this->error403();

        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $outlet = Auth::user()->outlet ?: $this->error403();

        $sales_invoice->load([
            "planned_sales_invoice_items",
            "planned_sales_invoice_items.menu_item",
            "planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) use($outlet) {
                $query->where("outlet_id", $outlet->id);
            }
        ]);

        if ($sales_invoice->status !== SalesInvoiceModel::FINISHED) {
            DB::transaction(function() use ($sales_invoice) {
                $sales_invoice->update([
                    "status" => SalesInvoiceModel::FINISHED,
                    "cash" => $this->input->post("cash"),
                ]);

                foreach ($sales_invoice->planned_sales_invoice_items as $sales_invoice_item) {
                    SalesInvoiceItem::create([
                        "sales_invoice_id" => $sales_invoice->id,
                        "name" => $sales_invoice_item->menu_item->name,
                        "price" => $sales_invoice_item->menu_item->outlet_menu_item->price,
                        "quantity" => $sales_invoice_item->quantity,
                    ]);
                }
            });
        }

        $cashierReceiptText = $this->cashierReceiptPrintRequest($sales_invoice);
        $this->jsonResponse($cashierReceiptText);
    }

    private function cashierReceiptText(SalesInvoiceModel $sales_invoice)
    {
        $sales_invoice->loadMissing([
            "outlet",
            "sales_invoice_items",
        ]);

        $text = "";

        /* Generate header */
        $text .= $sales_invoice->outlet->name . "\n";
        $text .= $sales_invoice->outlet->address . "\n";
        $text .= $sales_invoice->outlet->phone . "\n";
        $text .= "Tax Invoice" . "\n";
        $text .= sprintf("No %08d", $sales_invoice->id) . "\n";

        /* The row format */
        $column_01_length = self::RECEIPT_COLUMN_01_LENGTH;
        $column_02_length = self::RECEIPT_COLUMN_02_LENGTH;
        $format = "%-{$column_01_length}.{$column_01_length}s%{$column_02_length}.{$column_02_length}s\n";


        /* Receipt Items */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        foreach ($sales_invoice->sales_invoice_items as $sales_invoice_item) {
            $text .= sprintf(
                $format,
                "{$sales_invoice_item->name} x{$sales_invoice_item->quantity}",
                Formatter::currency($sales_invoice_item->price * $sales_invoice_item->quantity)
            );
        }


        /* Sub Total, Taxes, Services, Discount */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, "Sub Total", Formatter::currency($sales_invoice->pretax_total));
        $text .= sprintf($format, "Tax {$sales_invoice->outlet->pajak_pertambahan_nilai}%", Formatter::currency($sales_invoice->tax));
        $text .= sprintf($format, "Service Charge {$sales_invoice->outlet->service_charge}%", Formatter::currency($sales_invoice->service_charge));


        /* Cash Paid */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, "Cash", Formatter::currency($sales_invoice->cash));

        /* Rounding */
        $text .= sprintf($format, "Rounding", Formatter::currency($sales_invoice->rounding));

        $text .= "\n";

        /* Total Change */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, "Total Change", Formatter::currency($sales_invoice->total_change));


        return $text;
    }

    private function cashierReceiptPrintRequest(SalesInvoiceModel $sales_invoice)
    {
        $sales_invoice->loadMissing([
            "outlet.cashier_printer",
            "outlet.kitchen_printer",
        ]);

        $commands = [
            [
                "name" => "setJustification",
                "arguments" => [["data" => Printer::JUSTIFY_CENTER, "type" => "integer"]],
            ],
            [
                "name" => "text",
                "arguments" => [["data" => $this->cashierReceiptText($sales_invoice), "type" => "string"]],
            ],
            [
                "name" => "cut",
                "arguments" => [],
            ]
        ];

        return [
            "address" => $sales_invoice->outlet->cashier_printer->ipv4_address,
            "port" => $sales_invoice->outlet->cashier_printer->port,
            "commands" => $commands,
        ];
    }

    private function receiptTextSeparator($len, $char = "-") {
        $separator_text = "";
        for ($i = 0; $i < $len; $i++) { 
            $separator_text .= $char;
        }
        $separator_text .= "\n";
        return $separator_text;
    }

    public function updateAndConfirm($sales_invoice_id)
    {
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $sales_invoice->load("planned_sales_invoice_items");

        $outlet = Auth::user()->outlet ?: $this->error403();
        $outlet->load("outlet_menu_items:outlet_id,menu_item_id,price");
        $outlet->load("outlet_menu_items.menu_item");
        $outlet_menu_items = $outlet->outlet_menu_items;

        $this->template->render("sales_invoice/update_and_confirm", compact("sales_invoice", "outlet_menu_items"));
    }

    public function processUpdateAndConfirm($sales_invoice_id)
    {
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $outlet = Auth::user()->outlet ?: $this->error403();

        $this->validate([
            ["password", "kata sandi", "required"],
            ["menu_items[*][id]", "id item menu", "required",],
            ["menu_items[*][quantity]", "jumlah item menu", "required",],
            ["type", "tipe pemesanan", "required",],
        ]);

        if ($outlet->supervisor === null) {
            $this->error403();
        }

        if (!password_verify($this->input->post('password'), $outlet->supervisor->password)) {
            $this->error(403, "Kata sandi keliru");
        }
        
        $outlet_menu_items = OutletMenuItem::query()
            ->whereIn("menu_item_id", collect($this->input->post("menu_items"))->pluck("id"))
            ->where("outlet_id", $outlet->id)
            ->with("menu_item:id,name")
            ->get()
            ->keyBy("id");

        DB::transaction(function() use ($sales_invoice, $outlet_menu_items) {
            $sales_invoice->update([
                "status" => SalesInvoiceModel::FINISHED,
            ]);

            foreach ($this->input->post("menu_items") as $menu_item) {
                SalesInvoiceItem::create([
                    "sales_invoice_id" => $sales_invoice->id,
                    "name" => $outlet_menu_items[$menu_item["id"]]->menu_item->name,
                    "price" => $outlet_menu_items[$menu_item["id"]]->price,
                    "quantity" => $menu_item["quantity"],
                ]);
            }
        });

        $this->session->set_flashdata('message-success', 'Pesanan berhasil diselesaikan.');
    }

    public function store()
    {
        $this->validate([
            ["menu_items[*][id]", "id item menu", "required",],
            ["menu_items[*][quantity]", "jumlah item menu", "required",],
            ["type", "tipe pemesanan", "required",],
        ]);

        $data = $this->input->post(null);

        DB::transaction(function() use($data) {
            $sales_invoice_count = SalesInvoiceModel::query()
                ->whereDate("created_at", "=", Carbon::today())
                ->count();

            $this->sales_invoice = SalesInvoiceModel::create([
                "type" => $data["type"],
                "status" => SalesInvoiceModel::UNPAID,
                "number" => $sales_invoice_count + 1,
                "outlet_id" => Auth::user()->outlet->id,
            ]);

            foreach ($data["menu_items"] as $ordered_menu_item) {
                PlannedSalesInvoiceItem::create([
                    "sales_invoice_id" => $this->sales_invoice->id,
                    "menu_item_id" => $ordered_menu_item["id"],
                    "quantity" => $ordered_menu_item["quantity"],
                ]);
            }
        });

        $this->jsonResponse($this->sales_invoice);
    }
}