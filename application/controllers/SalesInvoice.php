<?php

use App\BaseController;
use Illuminate\Database\Capsule\Manager as DB;
use App\EloquentModels\SalesInvoice as SalesInvoiceModel;
use App\Helpers\Auth;
use App\EloquentModels\SalesInvoiceItem;
use App\EloquentModels\OutletMenuItem;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class SalesInvoice extends BaseController {
    private $printers;
    
    public function __construct()
    {
        parent::__construct();

        $receipt_printers = Auth::user()->outlet->receipt_printers;

        $this->printers = [];

        try {
            foreach ($receipt_printers as $receipt_printer) {
                $this->printers[] = new Printer(new NetworkPrintConnector($receipt_printer->ipv4_address, $receipt_printer->port));
            }
        }
        catch (\Exception $e) {

        }
    }

    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'create' => ['get'],
            'store' => ['post'],
            'edit' => ['get'],
            'update' => ['post'],
            'delete' => ['post'],
        ];
    }

    public function store()
    {
        $this->validate([
            ["menu_items[*][id]", "id item menu", "required",],
            ["menu_items[*][quantity]", "jumlah item menu", "required",],
        ]);

        $data = $this->input->post(null);

        $outlet_menu_items = OutletMenuItem::query()
            ->with("menu_item")
            ->where("outlet_id", Auth::user()->outlet->id)
            ->whereIn("menu_item_id", collect($data["menu_items"])->pluck("id"))
            ->get()
            ->keyBy("menu_item_id");

        $sales_invoice_items = collect();
        $print_text = "";

        DB::transaction(function() use($data, $outlet_menu_items, $sales_invoice_items, $print_text) {
            $sales_invoice = SalesInvoiceModel::create([
                "outlet_id" => Auth::user()->outlet->id,
            ]);

            foreach ($data["menu_items"] as $ordered_menu_item) {
                $sales_invoice_items->push(SalesInvoiceItem::create([
                    "sales_invoice_id" => $sales_invoice->id,
                    "name" => $outlet_menu_items[$ordered_menu_item["id"]]->menu_item->name,
                    "price" => $outlet_menu_items[$ordered_menu_item["id"]]->price,
                    "quantity" => $ordered_menu_item["quantity"],
                ]));
            }

            // Print data
            $print_text = $this->getPrintText($sales_invoice_items);

            foreach ($this->printers as $printer) {
                try {
                    $printer->text($print_text);
                }
                finally {
                    $printer->close();
                }
            }
        });

        echo $print_text;
    }

    private function getPrintText($sales_invoice_items)
    {
        $padding = 20;
        $text = "\n\n";
        
        foreach ($sales_invoice_items as $sales_invoice_item) {

            $text .= sprintf("%-{$padding}s", ($sales_invoice_item->quantity . "x " .  number_format($sales_invoice_item->price)));
            $text .= sprintf("Rp. %s", number_format($sales_invoice_item->quantity * $sales_invoice_item->price));
            $text .= "\n";

            $text .= strtoupper($sales_invoice_item->name);
            $text .= "\n\n";
        }

        $text .= "\n\n\n\n\n";
        return $text;
    }
}