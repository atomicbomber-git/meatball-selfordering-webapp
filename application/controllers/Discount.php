<?php

use App\BaseController;
use App\EloquentModels\Discount as DiscountModel;
use Illuminate\Database\Capsule\Manager as DB;
use App\EloquentModels\Outlet;

class Discount extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'create' => ['get'],
            'delete' => ['post'],
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

    public function store($discount_id)
    {
        ;
    }
}