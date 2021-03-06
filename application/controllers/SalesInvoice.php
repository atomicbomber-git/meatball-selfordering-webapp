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
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $sales_invoice->append("pretax_total", "tax_total", "service_charge_total", "total", "rounding", "total_change", "undiscounted_items");

        $outlet = Auth::user()->outlet ?: $this->error403();
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

        $sales_invoice->append("discount_map", "undiscounted_pretax_total", "special_discount_total", "prediscount_pretax_total");
        $this->template->render("sales_invoice/confirm", compact("sales_invoice"));
    }

    public function processConfirm($sales_invoice_id)
    {
        SalesInvoicePolicy::canConfirm(Auth::user()) ?: $this->error403();
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $sales_invoice->append("pretax_total", "tax_total", "service_charge_total", "total", "rounding", "total_change", "undiscounted_items");

        $this->validate([
            ["cash", "jumlah pembayaran", "required"],
            ["special_discount", "diskon spesial", "required"],
        ]);

        DB::transaction(function () use ($sales_invoice) {

            if ($sales_invoice->status !== SalesInvoiceModel::FINISHED) {

                foreach ($sales_invoice->planned_sales_invoice_items as $sales_invoice_item) {
                    SalesInvoiceItem::create([
                        "sales_invoice_id" => $sales_invoice->id,
                        "name" => $sales_invoice_item->menu_item->name,
                        "discount" => $sales_invoice->discount_map[$sales_invoice_item->menu_item->outlet_menu_item->id]->percentage ?? 0,
                        "price" => $sales_invoice_item->menu_item->outlet_menu_item->price,
                        "quantity" => $sales_invoice_item->quantity,
                    ]);
                }
            }

            $sales_invoice->update([
                "status" => SalesInvoiceModel::FINISHED,
                "cash" => $this->input->post("cash"),
                "special_discount" => $this->input->post("special_discount"),
                "cashier_id" => Auth::user()->id,

                "total_paid" => $sales_invoice->rounding,
                "pajak_pertambahan_nilai" => $sales_invoice->outlet->pajak_pertambahan_nilai,
                "service_charge" => $sales_invoice->outlet->service_charge,
            ]);
        });

        $this->validate([
            ["cash", "jumlah pembayaran", "required|greater_than_equal_to[{$sales_invoice->rounding}]"],
            ["special_discount", "diskon spesial", "required"],
        ]);

        $this->jsonResponse($this->cashierReceiptPrintRequest($sales_invoice));
    }

    public function updateAndConfirm($sales_invoice_id)
    {
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $sales_invoice->load(["planned_sales_invoice_items", "outlet",]);
        $sales_invoice->append("pretax_total", "tax_total", "service_charge_total", "total", "rounding", "total_change", "undiscounted_items", "discount_map");

        $outlet = $sales_invoice->outlet;
        $outlet->load([
            "outlet_menu_items:id,outlet_id,menu_item_id,price",
            "outlet_menu_items.menu_item"
        ]);

        $outlet_menu_items = $outlet->outlet_menu_items;

        $this->template->render("sales_invoice/update_and_confirm", compact("sales_invoice", "outlet_menu_items"));
    }

    public function processUpdateAndConfirm($sales_invoice_id)
    {
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $sales_invoice->append("pretax_total", "tax_total", "service_charge_total", "total", "rounding", "total_change", "undiscounted_items", "discount_map");

        $outlet = Auth::user()->outlet ?: $this->error403();

        $this->validate([
            ["password", "kata sandi", "required"],
            ["menu_items[*][id]", "id item menu", "required",],
            ["menu_items[*][quantity]", "jumlah item menu", "required",],
            ["type", "tipe pemesanan", "required",],
            ["cash", "jumlah pembayaran", "required"],
        ]);

        /* Authenticates the supervisor */
        if (!password_verify($this->input->post('password'), $outlet->supervisor->password)) {
            $this->error(403, "Kata sandi keliru");
        }

        /* It is important that the next three blocks are executed in this order */
        $print_requests = [];
        $kitchen_update_print_request = $this->kitchenUpdateReceiptPrintRequest($sales_invoice, $this->input->post("menu_items"));
        if ($kitchen_update_print_request !== null) {
            $print_requests[] = $kitchen_update_print_request;
        }

        /* Total price of the new set of items, with all the calcs performed */
        $rounding = null;
        DB::beginTransaction();

        $sales_invoice->update([
            "status" => SalesInvoiceModel::FINISHED,
            "special_discount" => $this->input->post("special_discount") ?: 0,
            "cash" => $this->input->post("cash"),
            "cashier_id" => Auth::user()->id,

            "total_paid" => $sales_invoice->rounding,
            "pajak_pertambahan_nilai" => $sales_invoice->outlet->pajak_pertambahan_nilai,
            "service_charge" => $sales_invoice->outlet->service_charge,
        ]);

        PlannedSalesInvoiceItem::where("sales_invoice_id", $sales_invoice->id)->delete();
        foreach ($this->input->post("menu_items") as $menu_item) {
            PlannedSalesInvoiceItem::create([
                "sales_invoice_id" => $sales_invoice->id,
                "menu_item_id" => $menu_item["id"],
                "quantity" => $menu_item["quantity"],
            ]);
        }

        $sales_invoice = SalesInvoiceModel::find($sales_invoice->id);
        $sales_invoice->append("pretax_total", "tax", "service_charge", "total", "rounding", "total_change", "undiscounted_items", "discount_map");

        $rounding = $sales_invoice->rounding;

        if ($this->input->post("cash") < $rounding) {
            DB::rollback();
        } else {
            DB::commit();
        }

        $this->validate([
            ["cash", "jumlah pembayaran", "greater_than_equal_to[{$rounding}]"],
        ]);

        $outlet_menu_items = OutletMenuItem::query()
            ->whereIn("menu_item_id", collect($this->input->post("menu_items"))->pluck("id"))
            ->where("outlet_id", $outlet->id)
            ->with("menu_item:id,name")
            ->get()
            ->keyBy("menu_item_id");

        DB::transaction(function () use ($sales_invoice, $outlet_menu_items) {
            SalesInvoiceItem::where("sales_invoice_id", $sales_invoice->id)->delete();
            foreach ($this->input->post("menu_items") as $menu_item) {
                SalesInvoiceItem::create([
                    "sales_invoice_id" => $sales_invoice->id,
                    "name" => $outlet_menu_items[$menu_item["id"]]->menu_item->name,
                    "price" => $outlet_menu_items[$menu_item["id"]]->price,
                    "discount" => $sales_invoice->discount_map[$outlet_menu_items[$menu_item["id"]]->id]->percentage ?? 0,
                    "quantity" => $menu_item["quantity"],
                ]);
            }
        });

        $print_requests[] = $this->cashierReceiptPrintRequest($sales_invoice);
        $this->jsonResponse($print_requests);
    }

    public function store()
    {
        $outlet = Auth::user()->outlet ?: $this->error403();

        $this->validate([
            ["menu_items[*][id]", "id item menu", "required",],
            ["menu_items[*][quantity]", "jumlah item menu", "required",],
            ["type", "tipe pemesanan", "required",],
        ]);

        $data = $this->input->post(null);

        DB::transaction(function () use ($data, $outlet) {
            $sales_invoice_count = SalesInvoiceModel::query()
                ->where("outlet_id", $outlet->id)
                ->whereDate("created_at", "=", Date::today())
                ->count();

            $invoice_number = SalesInvoiceModel::query()
                ->select("invoice_number")
                ->where("outlet_id", $outlet->id)
                ->orderByDesc("invoice_number")
                ->value("invoice_number") + 1 ?? 1;

            $this->sales_invoice = SalesInvoiceModel::create([
                "type" => $data["type"],
                "status" => SalesInvoiceModel::UNPAID,
                "number" => $sales_invoice_count + 1,
                "invoice_number" => $invoice_number,
                "outlet_id" => Auth::user()->outlet->id,
                "waiter_id" => Auth::user()->id,
            ]);

            foreach ($data["menu_items"] as $ordered_menu_item) {
                PlannedSalesInvoiceItem::create([
                    "sales_invoice_id" => $this->sales_invoice->id,
                    "menu_item_id" => $ordered_menu_item["id"],
                    "quantity" => $ordered_menu_item["quantity"],
                ]);
            }
        });

        $print_requests[] = $this->serviceInvoiceNumberPrintRequest($this->sales_invoice);
        $print_requests[] = $this->kitcheReceiptPrintRequest($this->sales_invoice);

        $this->jsonResponse([
            "sales_invoice" => $this->sales_invoice,
            "print_requests" => $print_requests,
        ]);
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
            return null;
        }

        $text = "";
        $format = $this->receiptRowFormat();

        $text .= sprintf($format, "NOMOR PESANAN", Formatter::salesInvoiceNumber($sales_invoice->number));
        $text .= $this->receiptTextSeparator();

        /* Order additions */
        if ($additions->count() > 0) {
            $text .= sprintf($format, "PENAMBAHAN", "");
            foreach ($additions as $menu_item_id => $quantity) {
                $outlet_menu_item = $outlet_menu_items[$menu_item_id];
                $text .= sprintf($format, "+{$quantity}x {$outlet_menu_item->menu_item->name}", "");
            }
        }

        if ($cancellations->count() > 0) {
            $text .= $this->receiptTextSeparator();

            /* Order cancellations */
            $text .= sprintf($format, "PEMBATALAN", "");
            foreach ($cancellations as $menu_item_id => $quantity) {
                $outlet_menu_item = $outlet_menu_items[$menu_item_id];
                $text .= sprintf($format, "{$quantity}x {$outlet_menu_item->menu_item->name}", "");
            }
        }

        return $text;
    }

    private function serviceInvoiceNumberPrintRequest(SalesInvoiceModel $sales_invoice)
    {
        $sales_invoice_number = Formatter::salesInvoiceNumber($sales_invoice->number);

        $commands = [
            [
                "name" => "setJustification",
                "arguments" => [["data" => Printer::JUSTIFY_CENTER, "type" => "integer"]],
            ],
            [
                "name" => "setTextSize",
                "arguments" => [["data" => 2, "type" => "integer"], ["data" => 2, "type" => "integer"]],
            ],
            [
                "name" => "text",
                "arguments" => [["data" => "Nomor Antrian Anda:\n\n\n", "type" => "string"]],
            ],
            [
                "name" => "setTextSize",
                "arguments" => [["data" => 4, "type" => "integer"], ["data" => 4, "type" => "integer"]],
            ],
            [
                "name" => "text",
                "arguments" => [["data" => "{$sales_invoice_number}\n\n\n", "type" => "string"]],
            ],
            [
                "name" => "cut",
                "arguments" => [],
            ],
        ];

        return [
            "address" => $sales_invoice->outlet->service_printer->ipv4_address,
            "port" => $sales_invoice->outlet->service_printer->port,
            "commands" => $commands,
        ];
    }

    private function kitcheReceiptPrintRequest(SalesInvoiceModel $sales_invoice)
    {
        $sales_invoice->loadMissing(["planned_sales_invoice_items.menu_item"]);

        $text = "";
        $format = $this->receiptRowFormat();


        $text .= sprintf($format, "NOMOR PESANAN", Formatter::salesInvoiceNumber($sales_invoice->number));
        $text .= $this->receiptTextSeparator();

        foreach ($sales_invoice->planned_sales_invoice_items as $psi_item) {
            $item_quantity = sprintf("%2s%s", $psi_item->quantity, "x {$psi_item->menu_item->name}");
            $text .= sprintf($format, $item_quantity, "");
        }

        $type_text = (SalesInvoiceModel::TYPES_EN[$sales_invoice->type] ?? "-") . "\n";
        $type_text .= $this->receiptTextSeparator();

        $commands = [
            [
                "name" => "selectPrintMode",
                "arguments" => [["data" => Printer::MODE_EMPHASIZED, "type" => "integer"]],
            ],
            [
                "name" => "text",
                "arguments" => [["data" => $type_text, "type" => "string"]],
            ],
            [
                "name" => "selectPrintMode",
                "arguments" => [["data" => Printer::MODE_FONT_A, "type" => "integer"]],
            ],
            [
                "name" => "text",
                "arguments" => [["data" => $text, "type" => "string"]],
            ],
            [
                "name" => "cut",
                "arguments" => [],
            ],
        ];

        return [
            "address" => $sales_invoice->outlet->kitchen_printer->ipv4_address,
            "port" => $sales_invoice->outlet->kitchen_printer->port,
            "commands" => $commands,
        ];
    }

    private function kitchenUpdateReceiptPrintRequest(SalesInvoiceModel $sales_invoice, $menu_item_input)
    {
        $sales_invoice->loadMissing(["outlet.kitchen_printer",]);

        $kitchen_update_receipt_text = $this->kitchenUpdateReceiptText($sales_invoice, $menu_item_input);
        if ($kitchen_update_receipt_text === null) {
            return null;
        }

        $commands = [
            [
                "name" => "setJustification",
                "arguments" => [["data" => Printer::JUSTIFY_CENTER, "type" => "integer"]],
            ],
            [
                "name" => "text",
                "arguments" => [["data" => $kitchen_update_receipt_text, "type" => "string"]],
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
        $text .= Formatter::salesInvoiceId($sales_invoice->invoice_number) . "\n";


        /* The row format */
        $format = $this->receiptRowFormat();


        /* Cashier info */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, $sales_invoice->cashier->name, "");
        $text .= sprintf($format, $sales_invoice->created_at->format("m/d/Y H:i:s"), "");


        /* Receipt Items */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        foreach ($sales_invoice->sales_invoice_items as $sales_invoice_item) {
            
            $item_quantity = sprintf("%-2s%s", $sales_invoice_item->quantity, "x {$sales_invoice_item->name}");

            $text .= sprintf(
                $format,
                $item_quantity,
                Formatter::currency($sales_invoice_item->price * $sales_invoice_item->quantity)
            );

            /* Item discount */
            if ($sales_invoice_item->discount != 0) {
                $text .= sprintf(
                    $format,
                    "    Diskon " . Formatter::percent($sales_invoice_item->discount),
                    "-" . Formatter::currency($sales_invoice_item->price * $sales_invoice_item->quantity * $sales_invoice_item->discount)
                );
            };
        }

        /* Sub Total, Taxes, Services, Discount */
        $text .= $this->receiptTextSeparator(self::RECEIPT_SEPARATOR_LENGTH);
        $text .= sprintf($format, "Sub Total", Formatter::currency($sales_invoice->pretax_total));
        $text .= sprintf($format, "Tax " . Formatter::percent($sales_invoice->outlet->pajak_pertambahan_nilai), Formatter::currency($sales_invoice->tax_total));
        $text .= sprintf($format, "Service Charge " . Formatter::percent($sales_invoice->outlet->service_charge), Formatter::currency($sales_invoice->service_charge_total));
        $text .= sprintf($format, "Special Discount " . Formatter::percent($sales_invoice->special_discount), "-" .  Formatter::currency($sales_invoice->special_discount_total));


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

    private function receiptRowFormat()
    {
        $column_01_length = self::RECEIPT_COLUMN_01_LENGTH;
        $column_02_length = self::RECEIPT_COLUMN_02_LENGTH;
        return "%-{$column_01_length}.{$column_01_length}s%{$column_02_length}.{$column_02_length}s\n";
    }
}
