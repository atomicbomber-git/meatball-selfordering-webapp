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

        $menu_data = MenuCategory::query()
            ->with(["menu_items" => function ($query) use($outlet) {
                $query
                    ->whereHas("outlet_menu_item", function ($query) use ($outlet) {
                        $query->where("outlet_id", $outlet->id);
                    })
                    ->with("outlet_menu_item");
            }])
            ->get();

        $this->template->render("home/show", compact("menu_data", "outlet"));
    }
}