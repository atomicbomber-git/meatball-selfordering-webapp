<?php

use App\BaseController;
use Illuminate\Database\Capsule\Manager as DB;
use App\EloquentModels\SalesInvoice as SalesInvoiceModel;
use App\Helpers\Auth;
use App\EloquentModels\PlannedSalesInvoiceItem;
use App\EloquentModels\SalesInvoiceItem;
use App\Policies\SalesInvoicePolicy;
use App\EloquentModels\OutletMenuItem;
use App\Helpers\Formatter;
use Mike42\Escpos\Printer;
use Carbon\Carbon as Date;

class SalesInvoice extends BaseController
{

    const RECEIPT_COLUMN_01_LENGTH = 27;
    const RECEIPT_COLUMN_02_LENGTH = 13;
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
            ->whereDate("created_at", Date::today())
            ->where("status", SalesInvoiceModel::UNPAID)
            ->orderBy("number")
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
            "planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) use ($outlet) {
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
        SalesInvoicePolicy::canConfirm(Auth::user()) ?: $this->error403();
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();

        $this->validate([
            ["cash", "jumlah pembayaran", "required|greater_than_equal_to[{$sales_invoice->rounding}]"],
        ]);

        DB::transaction(function () use ($sales_invoice) {

            if ($sales_invoice->status !== SalesInvoiceModel::FINISHED) {
                foreach ($sales_invoice->planned_sales_invoice_items as $sales_invoice_item) {
                    SalesInvoiceItem::create([
                        "sales_invoice_id" => $sales_invoice->id,
                        "name" => $sales_invoice_item->menu_item->name,
                        "price" => $sales_invoice_item->menu_item->outlet_menu_item->price,
                        "quantity" => $sales_invoice_item->quantity,
                    ]);
                }
            }

            $sales_invoice->update([
                "status" => SalesInvoiceModel::FINISHED,
                "cash" => $this->input->post("cash"),
                "cashier_id" => Auth::user()->id,
                "finished_at" => Date::now(),
            ]);
        });

        $this->jsonResponse($this->cashierReceiptPrintRequest($sales_invoice));
    }

    public function updateAndConfirm($sales_invoice_id)
    {
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $sales_invoice->load([
            "planned_sales_invoice_items", "outlet",
        ]);

        $outlet = $sales_invoice->outlet;
        $outlet->load([
            "outlet_menu_items:outlet_id,menu_item_id,price",
            "outlet_menu_items.menu_item"
        ]);

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

        if (!password_verify($this->input->post('password'), $outlet->supervisor->password)) {
            $this->error(403, "Kata sandi keliru");
        }

        $print_requests = [];
        $print_requests[] = $this->kitchenUpdateReceiptPrintRequest($sales_invoice, $this->input->post("menu_items"));

        $outlet_menu_items = OutletMenuItem::query()
            ->whereIn("menu_item_id", collect($this->input->post("menu_items"))->pluck("id"))
            ->where("outlet_id", $outlet->id)
            ->with("menu_item:id,name")
            ->get()
            ->keyBy("menu_item_id");

        DB::transaction(function () use ($sales_invoice, $outlet_menu_items) {
            $sales_invoice->update([
                "status" => SalesInvoiceModel::FINISHED,
                "cash" => 1000000,
                "cashier_id" => Auth::user()->id,
                "finished_at" => Date::now(),
            ]);

            PlannedSalesInvoiceItem::where("sales_invoice_id", $sales_invoice->id)->delete();
            foreach ($this->input->post("menu_items") as $menu_item) {
                PlannedSalesInvoiceItem::create([
                    "sales_invoice_id" => $sales_invoice->id,
                    "menu_item_id" => $menu_item["id"],
                    "quantity" => $menu_item["quantity"],
                ]);
            }

            SalesInvoiceItem::where("sales_invoice_id", $sales_invoice->id)->delete();
            foreach ($this->input->post("menu_items") as $menu_item) {
                SalesInvoiceItem::create([
                    "sales_invoice_id" => $sales_invoice->id,
                    "name" => $outlet_menu_items[$menu_item["id"]]->menu_item->name,
                    "price" => $outlet_menu_items[$menu_item["id"]]->price,
                    "quantity" => $menu_item["quantity"],
                ]);
            }
        });


        $print_requests[] = $this->cashierReceiptPrintRequest($sales_invoice);
        $this->jsonResponse($print_requests);
    }

    public function store()
    {
        $this->validate([
            ["menu_items[*][id]", "id item menu", "required",],
            ["menu_items[*][quantity]", "jumlah item menu", "required",],
            ["type", "tipe pemesanan", "required",],
        ]);

        $data = $this->input->post(null);

        DB::transaction(function () use ($data) {
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

    private function kitchenUpdateReceiptText(SalesInvoiceModel $sales_invoice, $menu_item_input)
    {
        $outlet_menu_items = OutletMenuItem::query()
            ->whereIn("menu_item_id", collect($menu_item_input)->pluck("id"))
            ->where("outlet_id", $sales_invoice->outlet->id)
            ->with("menu_item:id,name")
            ->get()
            ->keyBy("menu_item_id");

        $original_planned_sales_invoice_items = collect($sales_invoice->planned_sales_invoice_items)
            ->mapWithKeys(function ($planned_sales_invoice_item) {
                return [
                    $planned_sales_invoice_item->menu_item_id => $planned_sales_invoice_item->quantity,
                ];
            });

        $new_planned_sales_invoice_items = collect($menu_item_input)
            ->mapWithKeys(function ($planned_sales_invoice_item) {
                return [
                    (int)$planned_sales_invoice_item["id"] => (int)$planned_sales_invoice_item["quantity"]
                ];
            });

        /* Find quantity differences between the new sales invoice and the old one  */
        $diffed_planned_sales_invoice_items = collect()
            ->merge($original_planned_sales_invoice_items->keys())
            ->merge($new_planned_sales_invoice_items->keys())
            ->unique()
            ->mapWithKeys(function ($id) use ($original_planned_sales_invoice_items, $new_planned_sales_invoice_items) {
                return [$id => ($new_planned_sales_invoice_items[$id] ?? 0) - ($original_planned_sales_invoice_items[$id] ?? 0)];
            })
            ->filter(function ($difference) {
                return $difference !== 0;
            });

        $additions = $diffed_planned_sales_invoice_items
            ->filter(function ($difference) {
                return $difference > 0;
            });

        $cancellations = $diffed_planned_sales_invoice_items
            ->filter(function ($difference) {
                return $difference < 0;
            });

        if ($diffed_planned_sales_invoice_items->count() === 0) {
            return "";
        }

        $text = "";
        $format = $this->receiptRowFormat();


        /* Order additions */
        $text .= sprintf($format, "PENAMBAHAN", "");
        foreach ($additions as $menu_item_id => $quantity) {
            $outlet_menu_item = $outlet_menu_items[$menu_item_id];
            $text .= sprintf($format, "+{$quantity}x {$outlet_menu_item->menu_item->name}", "");
        }

        $text .= $this->receiptTextSeparator();

        /* Order cancellations */
        $text .= sprintf($format, "PEMBATALAN", "");
        foreach ($cancellations as $menu_item_id => $quantity) {
            $outlet_menu_item = $outlet_menu_items[$menu_item_id];
            $text .= sprintf($format, "{$quantity}x {$outlet_menu_item->menu_item->name}", "");
        }

        return $text;
    }

    private function kitchenUpdateReceiptPrintRequest(SalesInvoiceModel $sales_invoice, $menu_item_input)
    {
        $sales_invoice->loadMissing(["outlet.kitchen_printer",]);

        $commands = [
            [
                "name" => "setJustification",
                "arguments" => [["data" => Printer::JUSTIFY_CENTER, "type" => "integer"]],
            ],
            [
                "name" => "text",
                "arguments" => [["data" => $this->kitchenUpdateReceiptText($sales_invoice, $menu_item_input), "type" => "string"]],
            ],
            [
                "name" => "cut",
                "arguments" => [],
            ]
        ];

        return [
            "address" => $sales_invoice->outlet->kitchen_printer->ipv4_address,
            "port" => $sales_invoice->outlet->kitchen_printer->port,
            "commands" => $commands,
        ];
    }

    private function cashierReceiptText(SalesInvoiceModel $sales_invoice)
    {
        $sales_invoice->loadMissing(["cashier", "outlet", "sales_invoice_items"]);

        $text = "";

        /* Generate header */
        $text .= $sales_invoice->outlet->name . "\n";
        $text .= $sales_invoice->outlet->address . "\n";
        $text .= $sales_invoice->outlet->phone . "\n";
        $text .= "Tax Invoice" . "\n";
        $text .= sprintf("No %08d", $sales_invoice->id) . "\n";


        /* The row format */
        $format = $this->receiptRowFormat();


        /* Cashier info */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, $sales_invoice->cashier->name, "");
        $text .= sprintf($format, $sales_invoice->finished_at->format("m/d/Y H:i:s"), "");


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


        /* Total */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, "Total", Formatter::currency($sales_invoice->total));


        /* Cash Paid */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, "Cash", Formatter::currency($sales_invoice->cash));

        /* Rounding */
        $text .= sprintf($format, "Rounding", Formatter::currency($sales_invoice->rounding));

        /* Total Change */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, "Total Change", Formatter::currency($sales_invoice->total_change));


        /* Footer, thank you text and NPWPD */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= "NPWPD: {$sales_invoice->outlet->npwpd}\n";
        $text .= "Terima Kasih Atas Kunjungannya\nSilahkan Datang Kembali\n";

        return $text;
    }

    private function cashierReceiptPrintRequest(SalesInvoiceModel $sales_invoice)
    {
        $sales_invoice->loadMissing(["outlet.cashier_printer", "outlet.kitchen_printer",]);

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

    private function receiptTextSeparator($len = null, $char = "-")
    {
        if ($len === null) {
            $len = self::RECEIPT_COLUMN_01_LENGTH + self::RECEIPT_COLUMN_02_LENGTH;
        }

        $separator_text = "";
        for ($i = 0; $i < $len; $i++) {
            $separator_text .= $char;
        }
        $separator_text .= "\n";
        return $separator_text;
    }

    private function receiptRowFormat() {
        $column_01_length = self::RECEIPT_COLUMN_01_LENGTH;
        $column_02_length = self::RECEIPT_COLUMN_02_LENGTH;
        return "%-{$column_01_length}.{$column_01_length}s%{$column_02_length}.{$column_02_length}s\n";
    }
}
