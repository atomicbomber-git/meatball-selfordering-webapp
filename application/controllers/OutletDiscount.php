<?php

use App\BaseController;
use App\EloquentModels\Outlet;

class OutletDiscount extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'detail' => ['get'],
        ];
    }

    public function index()
    {
        $outlets = Outlet::select("id", "name")->get();
        $this->template->render("outlet_discount/index", compact("outlets"));
    }

    public function detail($outlet_id)
    {
        $outlet = Outlet::find($outlet_id) ?: $this->error404();

        $outlet->load("discounts");

        $this->template->render("outlet_discount/detail", compact("outlet"));
    }
}