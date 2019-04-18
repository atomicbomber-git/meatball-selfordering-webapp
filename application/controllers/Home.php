<?php

use App\BaseController;
use App\EloquentModels\ItemType;
use App\EloquentModels\MenuCategory;

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
        // $menu_data = 

        $this->jsonResponse(MenuCategory::all());
        $this->template->render("home/show");
    }
}