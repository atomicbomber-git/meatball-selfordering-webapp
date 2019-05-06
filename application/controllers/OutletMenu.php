<?php

use App\BaseController;
use App\EloquentModels\Outlet;
use App\EloquentModels\MenuCategory;

class OutletMenu extends BaseController {
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
        $this->template->render("outlet_menu/index", compact("outlets"));
    }

    public function detail($outlet_id)
    {
        $outlet = Outlet::find($outlet_id) ?: $this->error404();
        $menu_categories = MenuCategory::query()
            ->select("id", "name")
            ->orderBy("id")
            ->get();

        $this->template->render("outlet_menu/detail", compact("outlet", "menu_categories"));
    }
}