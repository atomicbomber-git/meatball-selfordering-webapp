<?php

use App\BaseController;
use App\EloquentModels\Outlet;

class OutletDiscountMenu extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
        ];
    }

    public function index()
    {
        $outlets = Outlet::select("id", "name")->get();
        $this->template->render("outlet_discount/index", compact("outlets"));
    }
}