<?php

use App\BaseController;
use App\EloquentModels\MenuCategory;
use App\Helpers\Auth;
use App\Helpers\DefaultRoute;

class Home extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->authorize();
    }

    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'show' => ['get'],
        ];
    }

    public function index()
    {
        redirect(base_url(DefaultRoute::get()));
    }

    public function show()
    {
        $outlet = Auth::user()->outlet ?: $this->error403();
        $outlet->append("discount_map");
        $outlet->load(["outlet_menu_items:outlet_id,menu_item_id,price",]);

        $menu_data = MenuCategory::query()
            ->with(["menu_items" => function ($query) use($outlet) {
                $query->whereIn("id", $outlet->outlet_menu_items->pluck("menu_item_id"));
            }])
            ->with(["menu_items.outlet_menu_item" => function ($query) use ($outlet) {
                $query
                    ->select("id", "outlet_id", "price", "menu_item_id")
                    ->where("outlet_id", $outlet->id);
            }])
            ->get();

        $this->template->render("home/show", compact("menu_data", "outlet"));
    }
}