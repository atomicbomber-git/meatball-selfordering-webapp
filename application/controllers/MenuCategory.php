<?php

use App\BaseController;
use App\EloquentModels\MenuCategory as MenuCategoryModel;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Policies\MenuCategoryPolicy;
use App\Helpers\Auth;
use App\Validators\IsUniqueExcept;

class MenuCategory extends BaseController {
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
            'image' => ['get'],
        ];
    }

    public function index()
    {
        MenuCategoryPolicy::canIndex(Auth::user()) ?: $this->error403();

        $menu_categories = MenuCategoryModel::query()
            ->withCount("menu_items")
            ->orderBy("column")
            ->orderBy("priority")
            ->get();
        
        $this->template->render("menu_category/index", compact("menu_categories"));
    }

    public function create()
    {
        MenuCategoryPolicy::canCreate(Auth::user()) ?: $this->error403();

        $this->template->render("menu_category/create");
    }

    public function store()
    {
        MenuCategoryPolicy::canCreate(Auth::user()) ?: $this->error403();

        $this->validate([
            ["name", "nama", "required|is_unique[menu_categories.name]"],
            ["priority", "prioritas", "required"],
            ["column", "nomor kolom", "required"],
        ]);

        Capsule::transaction(function() {
            $menu_category = MenuCategoryModel::create([
                "name" => $this->input->post("name"),
                "priority" => $this->input->post("priority"),
                "column" => $this->input->post("column"),
            ]);
        });

        $this->session->set_flashdata('message-success', 'Data berhasil ditambahkan.');
        redirect("menuCategory/index");
    }

    public function edit($menu_category_id)
    {
        MenuCategoryPolicy::canUpdate(Auth::user()) ?: $this->error403();

        $menu_category = MenuCategoryModel::find($menu_category_id) ?: $this->error404();
        $this->template->render("menu_category/edit", compact("menu_category"));
    }

    public function update($menu_category_id)
    {
        MenuCategoryPolicy::canUpdate(Auth::user()) ?: $this->error403();
        $menu_category = MenuCategoryModel::find($menu_category_id) ?: $this->error404();

        $this->validate([
            ["name", "nama", ["required", IsUniqueExcept::validator("menu_categories.name", $menu_category->name)]],
            ["priority", "prioritas", "required"],
            ["column", "nomor kolom", "required"],
        ]);

        Capsule::transaction(function() use($menu_category) {
            $menu_category->update([
                "name" => $this->input->post("name"),
                "priority" => $this->input->post("priority"),
                "column" => $this->input->post("column"),
            ]);
        });

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function delete($menu_category_id)
    {
        $menu_category = MenuCategoryModel::find($menu_category_id) ?: $this->error404();

        if (! MenuCategoryPolicy::canDelete(Auth::user(), $menu_category)) {
            $this->session->set_flashdata('message-danger', 'Data tidak dapat dihapus.');
            $this->redirectBack();
        }

        $menu_category->delete();
        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');
        $this->redirectBack();
    }

    public function image($menu_category_id)
    {
        $menu_category = MenuCategoryModel::find($menu_category_id) ?: $this->error404();
        $menu_category->image ?: $this->error404();
        $image_mime_type = mime_content_type($menu_category->image);
        $image_file = file_get_contents($menu_category->image);

        $this->output
            ->set_header("Cache-Control: public, max-age=300, s-maxage=300")
            ->set_content_type($image_mime_type)
            ->set_output($image_file);
    }
}