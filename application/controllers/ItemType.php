<?php

use App\BaseController;
use App\EloquentModels\ItemType as ItemTypeModel;

class ItemType extends BaseController {
    public function __construct()
    {
        parent::__construct();
        $this->authorize();
    }

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

    public function index()
    {
        $item_types = ItemTypeModel::query()
            ->whereHas("item_group", function ($query) {
                $query->where("name", "PRODUCT");
            })
            ->get();

        $this->template->render("item_type/index", compact("item_types"));
    }

    public function create()
    {
        $this->template->render("item_type/create");
    }
}