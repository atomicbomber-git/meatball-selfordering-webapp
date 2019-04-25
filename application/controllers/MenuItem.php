<?php

use App\BaseController;
use App\EloquentModels\MenuItem as MenuItemModel;
use App\EloquentModels\MenuCategory;

class MenuItem extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'create' => ['get'],
            'store' => ['post'],
            'edit' => ['get'],
            'update' => ['post'],
            'delete' => ['post'],
        ];
    }

    public function index($menu_category_id)
    {
        $menu_category = MenuCategory::find($menu_category_id) ?: $this->error404();
        $menu_category->load("menu_items");

        $this->template->render("menu_item/index", compact("menu_category"));
    }
}