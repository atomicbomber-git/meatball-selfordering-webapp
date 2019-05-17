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
            'store' => ['post'],
            'delete' => ['post'],
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

    public function create($outlet_id, $menu_category_id)
    {
        $outlet = Outlet::find($outlet_id) ?: $this->error404();
        $menu_category = MenuCategory::find($menu_category_id) ?: $this->error404();

        $menu_category->load(["menu_items" => function ($query) use($outlet) {
            $query->whereDoesntHave("outlet_menu_item", function ($query) use($outlet) {
                $query->where("outlet_id", $outlet->id);
            });
        }]);

        $this->template->render("outlet_menu_item/create", compact("outlet", "menu_category"));
    }

    public function store($outlet_id)
    {
        Outlet::find($outlet_id) ?: $this->error404();

        $this->validate([
            ["menu_item_id", "ID menu item", "required"],
            ["price", "harga", "required"],
        ]);
        
        OutletMenuItemModel::create([
            "outlet_id" => $outlet_id,
            "menu_item_id" => $this->input->post("menu_item_id"),
            "price" => $this->input->post("price")
        ]);

        $this->session->set_flashdata('message-success', 'Data berhasil ditambahkan.');
    }

    public function edit($outlet_menu_item_id)
    {
        $outlet_menu_item = OutletMenuItemModel::find($outlet_menu_item_id) ?: $this->error404();
        $this->template->render("outlet_menu_item/edit", compact("outlet_menu_item"));
    }

    public function delete($outlet_menu_item_id)
    {
        $outlet_menu_item = OutletMenuItemModel::find($outlet_menu_item_id) ?: $this->error404();
        $outlet_menu_item->delete();

        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');
        $this->redirectBack();
    }
}