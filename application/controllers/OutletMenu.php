<?php

use App\BaseController;
use App\EloquentModels\Outlet;
use App\EloquentModels\MenuCategory;
use App\Policies\OutletMenuPolicy;
use App\Helpers\Auth;

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
        OutletMenuPolicy::canIndex(Auth::user()) ?:
            $this->error403();

        $outlets = Outlet::select("id", "name")->get();
        $this->template->render("outlet_menu/index", compact("outlets"));
    }

    public function detail($outlet_id)
    {
        $outlet = Outlet::find($outlet_id) ?: $this->error404();

        OutletMenuPolicy::canIndex(Auth::user()) ?:
            $this->error403();

        $menu_categories = MenuCategory::query()
            ->select("id", "name")
            ->withCount(["outlet_menu_items" => function ($query) use ($outlet_id) {
                $query->where("outlet_id", $outlet_id);
            }])
            ->orderBy("id")
            ->get();

        $this->template->render("outlet_menu/detail", compact("outlet", "menu_categories"));
    }
}