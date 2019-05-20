<?php

use App\BaseController;
use App\EloquentModels\MenuCategory as MenuCategoryModel;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Policies\MenuCategoryPolicy;
use App\Helpers\Auth;

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

        $menu_categories = MenuCategoryModel::all();
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
            ["name", "nama", "required|is_unique[menu_categories.name]"]
        ]);

        Capsule::transaction(function() {
            $menu_category = MenuCategoryModel::create([
                "name" => $this->input->post("name"),
            ]);
    
            $this->load->library('upload', [
                'upload_path' => MenuCategoryModel::IMAGE_STORAGE_PATH,
                'allowed_types' => 'jpg|jpeg|png',
                'file_name' => "{$menu_category->id}",
                'max_size' => 1024 * 40, // 40 Megabytes
            ]);
    
            if ( ! $this->upload->do_upload('image')) {
                $this->session->set_flashdata("errors", array_merge(
                    $this->session->flashdata(),
                    ["image" => $this->upload->display_errors('', '')]
                ));

                $this->saveOldFormData();
                $this->redirectBack();
                throw new \Exception("Image upload failed.");
            }
    
            $upload_data = $this->upload->data();
            
            $menu_category->update([
                "image" => MenuCategoryModel::IMAGE_STORAGE_PATH . "/" . $upload_data["orig_name"]
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

        Capsule::transaction(function() use($menu_category) {
            $menu_category->update([
                "name" => $this->input->post("name"),
            ]);

            if (!empty($_FILES["image"]["name"])) {
                $this->load->library('upload', [
                    'upload_path' => MenuCategoryModel::IMAGE_STORAGE_PATH,
                    'allowed_types' => 'jpg|jpeg|png',
                    'file_name' => "{$menu_category->id}",
                    'overwrite' => TRUE,
                    'max_size' => MenuCategoryModel::IMAGE_MAX_SIZE, // 40 Megabytes
                ]);

                if ( ! $this->upload->do_upload('image')) {
                    $this->session->set_flashdata("errors", array_merge(
                        $this->session->flashdata(),
                        ["image" => $this->upload->display_errors('', '')]
                    ));

                    $this->redirectBack();
                    throw new \Exception("Image upload failed.");
                }

                $upload_data = $this->upload->data();
                $menu_category->update(["image" => MenuCategoryModel::IMAGE_STORAGE_PATH . "/" . $upload_data["orig_name"]]);
            }
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