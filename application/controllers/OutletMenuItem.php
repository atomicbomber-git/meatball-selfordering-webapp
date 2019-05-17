<?php

use App\BaseController;
use App\EloquentModels\Outlet;
use App\EloquentModels\MenuCategory;
use App\EloquentModels\OutletMenuItem as OutletMenuItemModel;

class OutletMenuItem extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'create' => ['get'],
        ];
    }

    public function index($outlet_id, $menu_category_id)
    {
        $outlet = Outlet::find($outlet_id);
        $menu_category = MenuCategory::find($menu_category_id);

        $outlet_menu_items = OutletMenuItemModel::query()
            ->with("menu_item")
            ->where("outlet_id", $outlet->id)
            ->whereHas("menu_item", function ($query) use($menu_category) {
                $query->where("menu_category_id", $menu_category->id);
            })
            ->get();

        $this->template->render("outlet_menu_item/index", compact("outlet", "menu_category", "outlet_menu_items"));
    }

    public function create($outlet_id)
    {
        $outlet = Outlet::find($outlet_id);

        
    }
}