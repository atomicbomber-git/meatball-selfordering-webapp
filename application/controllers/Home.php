<?php

use App\BaseController;
use App\EloquentModels\ItemType;
use App\EloquentModels\MenuCategory;
use App\Helpers\Auth;

class Home extends BaseController
{
    protected function allowedMethods()
    {
        return [
            'show' => ['get'],
        ];
    }

    public function show()
    {
        $outlet = Auth::user()->managed_outlet;
        $outlet->load("outlet_menu_items:outlet_id,menu_item_id,price");

        $menu_item_price_map = $outlet->outlet_menu_items
            ->mapWithKeys(function ($menu_item) {
                return [$menu_item->menu_item_id => $menu_item->price];
            });

        $menu_data = MenuCategory::query()
            ->with(["menu_items" => function ($query) use($outlet) {
                $query->whereIn("id", $outlet->outlet_menu_items->pluck("menu_item_id"));
            }])
            ->get()
            ->map(function ($menu_category) use($menu_item_price_map) {
                foreach ($menu_category->menu_items as $menu_item) {
                    $menu_item->price = $menu_item_price_map[$menu_item->id];
                }

                return $menu_category;
            });

        $this->template->render("home/show", compact("menu_data"));
    }
}