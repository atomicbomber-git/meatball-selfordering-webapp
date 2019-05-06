<?php

use App\BaseController;
use App\EloquentModels\MenuItem as MenuItemModel;
use App\EloquentModels\MenuCategory;
use App\Policies\MenuItemPolicy;
use App\Helpers\Auth;

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

    public function create($menu_category_id)
    {
        $menu_category = MenuCategory::find($menu_category_id) ?: $this->error404();
        $this->template->render("menu_item/create", compact("menu_category"));
    }

    public function store($menu_category_id)
    {
        $menu_category = MenuCategory::find($menu_category_id) ?: $this->error404();

        $this->validate([
            ["name", "nama menu", "required"],
        ]);

        MenuItemModel::create([
            "menu_category_id" => $menu_category_id,
            "name" => $this->input->post("name"),
        ]);

        $this->session->set_flashdata('message-success', 'Data berhasil ditambahkan.');
        redirect(base_url("menuItem/index/{$menu_category->id}"));
    }

    public function edit($menu_item_id)
    {
        $menu_item = MenuItemModel::find($menu_item_id) ?: $this->error404();
        $menu_item->load("menu_category:id,name");

        $this->template->render("menu_item/edit", compact("menu_item"));
    }

    public function update($menu_item_id)
    {
        $menu_item = MenuItemModel::find($menu_item_id) ?: $this->error404();

        $this->validate([
            ["name", "nama menu", "required"],
        ]);

        $menu_item->update([
            "name" => $this->input->post("name"),
        ]);
        
        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function delete($menu_item_id)
    {
        $menu_item = MenuItemModel::find($menu_item_id) ?: $this->error404();

        if (!MenuItemPolicy::canDelete(Auth::user(), $menu_item)) {
            $this->session->set_flashdata('message-danger', 'Data tidak dapat dihapus.');
            $this->redirectBack();
        }

        $menu_item->delete();

        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');
        redirect(base_url("menuItem/index/{$menu_item->menu_category_id}"));
    }
}