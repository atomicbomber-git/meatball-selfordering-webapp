<?php

use App\BaseController;
use Illuminate\Database\Capsule\Manager as DB;
use App\EloquentModels\SalesInvoice as SalesInvoiceModel;
use App\Helpers\Auth;
use Illuminate\Support\Carbon;
use App\EloquentModels\PlannedSalesInvoiceItem;
use App\EloquentModels\Outlet;
use App\EloquentModels\SalesInvoiceItem;

class SalesInvoice extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'create' => ['get'],
            'confirm' => ['get'],
            'processConfirm' => ['post'],
            'store' => ['post'],
            'edit' => ['get'],
            'update' => ['post'],
            'delete' => ['post'],
        ];
    }

    private $sales_invoice;

    public function index()
    {
        $sales_invoices = SalesInvoiceModel::query()
            ->whereDate("created_at", Carbon::today())
            ->where("status", SalesInvoiceModel::UNPAID)
            ->get();

        $this->template->render("sales_invoice/index", compact("sales_invoices"));
    }

    public function confirm($sales_invoice_id)
    {
        $outlet = Auth::user()->outlet ?: $this->error403();

        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $sales_invoice->load([
            "planned_sales_invoice_items",
            "planned_sales_invoice_items.menu_item",
            "planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) use($outlet) {
                $query->where("outlet_id", $outlet->id);
            }
        ]);

        $this->template->render("sales_invoice/confirm", compact("sales_invoice"));
    }

    public function processConfirm($sales_invoice_id)
    {
        $sales_invoice = SalesInvoiceModel::find($sales_invoice_id) ?: $this->error404();
        $outlet = Auth::user()->outlet ?: $this->error403();

        $sales_invoice->load([
            "planned_sales_invoice_items",
            "planned_sales_invoice_items.menu_item",
            "planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) use($outlet) {
                $query->where("outlet_id", $outlet->id);
            }
        ]);

        DB::transaction(function() use ($sales_invoice) {
            
            $sales_invoice->update([
                "status" => SalesInvoiceModel::FINISHED,
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

        $this->session->set_flashdata('message-success', 'Pesanan berhasil diselesaikan.');
        redirect("salesInvoice/index");
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