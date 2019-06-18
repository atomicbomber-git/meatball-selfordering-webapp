<?php

use App\BaseController;
use App\EloquentModels\Outlet;
use App\EloquentModels\MenuCategory;
use App\EloquentModels\OutletMenuItem as OutletMenuItemModel;
use App\Policies\OutletMenuItemPolicy;
use App\Helpers\Auth;

class OutletMenuItem extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'create' => ['get'],
            'store' => ['post'],
            'edit' => ['get'],
            'update' => ['post'],
            'delete' => ['post'],
            'activate' => ['post'],
            'deactivate' => ['post'],
        ];
    }

    public function index($outlet_id, $menu_category_id)
    {
        $outlet = Outlet::find($outlet_id) ?: $this->error404();
        $menu_category = MenuCategory::find($menu_category_id) ?: $this->error404();

        OutletMenuItemPolicy::canIndex(Auth::user()) ?:
            $this->error403();

        $outlet_menu_items = OutletMenuItemModel::query()
            ->withoutGlobalScope("active")
            ->with("menu_item")
            ->where("outlet_id", $outlet->id)
            ->whereHas("menu_item", function ($query) use($menu_category) {
                $query->where("menu_category_id", $menu_category->id);
            })
            ->orderBy("priority")
            ->get();

        $this->template->render("outlet_menu_item/index", compact("outlet", "menu_category", "outlet_menu_items"));
    }

    public function activate($outlet_menu_item_id)
    {
        $outlet_menu_item = OutletMenuItemModel::withoutGlobalScopes()
            ->find($outlet_menu_item_id) ?: $this->error404();

        $outlet_menu_item->update(["is_active" => true]);

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function deactivate($outlet_menu_item_id)
    {
        $outlet_menu_item = OutletMenuItemModel::withoutGlobalScopes()
            ->find($outlet_menu_item_id) ?: $this->error404();

        $outlet_menu_item->update(["is_active" => false]);
        
        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function create($outlet_id, $menu_category_id)
    {
        $outlet = Outlet::find($outlet_id) ?: $this->error404();
        $menu_category = MenuCategory::find($menu_category_id) ?: $this->error404();

        OutletMenuItemPolicy::canCreate(Auth::user()) ?:
            $this->error403();

        $menu_category->load(["menu_items" => function ($query) use($outlet) {
            $query->whereDoesntHave("outlet_menu_item", function ($query) use($outlet) {
                $query->withoutGlobalScopes()
                    ->where("outlet_id", $outlet->id);
            });
        }]);

        $this->template->render("outlet_menu_item/create", compact("outlet", "menu_category"));
    }

    public function store($outlet_id)
    {
        Outlet::find($outlet_id) ?: $this->error404();

        OutletMenuItemPolicy::canCreate(Auth::user()) ?:
            $this->error403();

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
        $outlet_menu_item = OutletMenuItemModel::withoutGlobalScopes()->find($outlet_menu_item_id)
            ?: $this->error404();

        OutletMenuItemPolicy::canUpdate(Auth::user()) ?:
            $this->error403();
        
        $outlet_menu_item->load([
            "outlet:id,name",
            "menu_item:id,name,menu_category_id",
            "menu_item.menu_category:id,name",
        ]);

        $this->template->render("outlet_menu_item/edit", compact("outlet_menu_item"));
    }

    public function update($outlet_menu_item_id)
    {
        $outlet_menu_item = OutletMenuItemModel::withoutGlobalScopes()->find($outlet_menu_item_id)
            ?: $this->error404();

        OutletMenuItemPolicy::canUpdate(Auth::user()) ?:
            $this->error403();

        $this->validate([
            ["price", "harga", "required"],
            ["priority", "prioritas", "required"],
        ]);

        $outlet_menu_item->update([
            "price" => $this->input->post("price"),
            "priority" => $this->input->post("priority"),
        ]);

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function delete($outlet_menu_item_id)
    {
        $outlet_menu_item = OutletMenuItemModel::withoutGlobalScopes()->find($outlet_menu_item_id)
            ?: $this->error404();

        OutletMenuItemPolicy::canDelete(Auth::user(), $outlet_menu_item) ?:
            $this->error403();

        $outlet_menu_item->delete();

        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');
        $this->redirectBack();
    }
}