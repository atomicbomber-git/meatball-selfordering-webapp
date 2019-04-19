<?php

use App\BaseController;
use Illuminate\Database\Capsule\Manager as DB;
use App\EloquentModels\SalesInvoice as SalesInvoiceModel;
use App\Helpers\Auth;
use App\EloquentModels\SalesInvoiceItem;
use App\EloquentModels\OutletMenuItem;

class SalesInvoice extends BaseController {
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

        DB::transaction(function() use($data, $outlet_menu_items) {
            $sales_invoice = SalesInvoiceModel::create([
                "outlet_id" => Auth::user()->outlet->id,
            ]);

            foreach ($data["menu_items"] as $ordered_menu_item) {
                SalesInvoiceItem::create([
                    "sales_invoice_id" => $sales_invoice->id,
                    "name" => $outlet_menu_items[$ordered_menu_item["id"]]->menu_item->name,
                    "price" => $outlet_menu_items[$ordered_menu_item["id"]]->price,
                    "quantity" => $ordered_menu_item["quantity"],
                ]);
            }
        });
    }
}