<?php

use App\BaseController;
use App\EloquentModels\Discount as DiscountModel;
use Illuminate\Database\Capsule\Manager as DB;
use App\EloquentModels\Outlet;
use App\EloquentModels\DiscountMenuItem;

class Discount extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'create' => ['get'],
            'store' => ['post'],
            'delete' => ['post'],
            'edit' => ['get'],
            'update' => ['post'],
        ];
    }

    public function create($outlet_id)
    {
        $outlet = Outlet::find($outlet_id) ?: $this->error404();
        $outlet->load("outlet_menu_items.menu_item");
        $this->template->render("discount/create", compact("outlet"));
    }

    public function delete($discount_id)
    {
        $discount = DiscountModel::find($discount_id) ?: $this->error404();

        DB::transaction(function() use($discount) {
            $discount->discount_menu_items()->delete();
            $discount->delete();
        });
        
        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');
        $this->redirectBack();
    }

    public function store($outlet_id)
    {
        Outlet::find($outlet_id) ?: $this->error404();

        $this->validate([
            ["name", "nama program diskon", "required"],
            ["starts_at", "waktu mulai diskon", "required"],
            ["ends_at", "waktu selesai diskon", "required"],
            ["outlet_menu_item[*][id]", "id menu outlet", "required"],
            ["outlet_menu_item[*][percentage]", "persentase diskon", "required"],
        ]);

        DB::transaction(function() use($outlet_id) {
            $discount = DiscountModel::create([
                "outlet_id" => $outlet_id,
                "name" => $this->input->post("name"),
                "starts_at" => $this->input->post("starts_at"),
                "ends_at" => $this->input->post("ends_at"),
            ]);
            
            $outlet_menu_items = $this->input->post("outlet_menu_items");

            foreach ($outlet_menu_items as $outlet_menu_item) {
                DiscountMenuItem::create([
                    "discount_id" => $discount->id,
                    "percentage" => $outlet_menu_item["percentage"] / 100,
                    "outlet_menu_item_id" => $outlet_menu_item["id"],
                ]);
            }
        });

        $this->session->set_flashdata('message-success', 'Data berhasil ditambahkan.');
    }

    public function edit($discount_id)
    {
        $discount = DiscountModel::find($discount_id) ?: $this->error404();
        
        $discount->load([
            "discount_menu_items:id,discount_id,percentage,outlet_menu_item_id",
            "discount_menu_items.outlet_menu_item:id,menu_item_id,price",
            "discount_menu_items.outlet_menu_item.menu_item:id,name",
            "outlet:id,name",
            "outlet.outlet_menu_items:id,outlet_id,menu_item_id,price",
            "outlet.outlet_menu_items.menu_item:id,name",
        ]);

        $this->template->render("discount/edit", compact("discount"));
    }

    public function update($discount_id)
    {
        $discount = DiscountModel::find($discount_id) ?: $this->error404();

        $this->validate([
            ["name", "nama program diskon", "required"],
            ["starts_at", "waktu mulai diskon", "required"],
            ["ends_at", "waktu selesai diskon", "required"],
            ["outlet_menu_item[*][id]", "id menu outlet", "required"],
            ["outlet_menu_item[*][percentage]", "persentase diskon", "required"],
        ]);

        DB::transaction(function() use($discount) {
            $discount->update([
                "name" => $this->input->post("name"),
                "starts_at" => $this->input->post("starts_at"),
                "ends_at" => $this->input->post("ends_at"),
            ]);

            $discount->discount_menu_items()->delete();

            $outlet_menu_items = $this->input->post("outlet_menu_items");
            foreach ($outlet_menu_items as $outlet_menu_item) {
                DiscountMenuItem::create([
                    "discount_id" => $discount->id,
                    "percentage" => $outlet_menu_item["percentage"] / 100,
                    "outlet_menu_item_id" => $outlet_menu_item["id"],
                ]);
            }
        });

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
    }
}