<?php

use App\BaseController;
use App\EloquentModels\MenuCategoryItem as MenuCategoryItemModel;

class MenuCategoryItem extends BaseController {
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
        $this->jsonResponse(MenuCategoryItemModel::all());
    }
}