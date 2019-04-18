<?php

use App\BaseController;
use App\EloquentModels\ItemType as ItemTypeModel;
use App\EloquentModels\ItemGroup;
use App\Enums\ItemGroupName;

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
            'image' => ['get'],
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

    public function store()
    {
        $this->validate([
            ["name", "nama", "required|is_unique[item_types.name]"]
        ]);
        
        $this->db->trans_start();

        // Item Group
        $item_group = ItemGroup::where("name", ItemGroupName::PRODUCT)->first();

        $item_type = ItemTypeModel::create([
            "name" => $this->input->post("name"),
            "item_group_id" => $item_group->id,
        ]);

        $this->load->library('upload', [
            'upload_path' => ItemTypeModel::IMAGE_STORAGE_PATH,
            'allowed_types' => 'jpg|jpeg|png',
            'file_name' => "{$item_type->id}",
            'max_size' => 1024 * 40, // 40 Megabytes
        ]);

        if ( ! $this->upload->do_upload('image')) {
            $this->session->set_flashdata("errors", array_merge(
                $this->session->flashdata(),
                ["image" => $this->upload->display_errors('', '')]
            ));

            $this->redirectBack();
        }

        $upload_data = $this->upload->data();
        
        $item_type->update([
            "image" => ItemTypeModel::IMAGE_STORAGE_PATH . "/" . $upload_data["orig_name"]
        ]);

        $this->db->trans_complete();
        redirect("itemType/index");
    }

    public function delete($item_type_id)
    {
        $item_type = ItemTypeModel::find($item_type_id) ?: $this->error404();
        $item_type->delete();

        $this->redirectBack();
    }

    public function image($item_type_id)
    {
        $item_type = ItemTypeModel::find($item_type_id) ?: $this->error404();
        $item_type->image ?: $this->error404();
        $image_mime_type = mime_content_type($item_type->image);
        $image_file = file_get_contents($item_type->image);

        $this->output
            ->set_content_type($image_mime_type)
            ->set_output($image_file);
    }
}