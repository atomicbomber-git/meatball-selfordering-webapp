<?php

use App\BaseController;
use App\EloquentModels\Outlet as OutletModel;
use App\Policies\OutletPolicy;
use App\Helpers\Auth;

class Outlet extends BaseController {
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
        OutletPolicy::canIndex(Auth::user()) ?: $this->error403();

        $outlets = OutletModel::query()
            ->select("id", "name", "address")
            ->withCount(OutletModel::RELATED_ENTITIES)
            ->get();

        $this->template->render("outlet/index", compact("outlets"));
    }

    public function create()
    {
        OutletPolicy::canCreate(Auth::user()) ?: $this->error403();

        $this->template->render("outlet/create");
    }

    public function store()
    {
        OutletPolicy::canCreate(Auth::user()) ?: $this->error403();

        $this->validate([
            ["name", "nama", "required"],
            ["address", "alamat", "required"],
            ["phone", "nomor telefon", "required"],
            ["pajak_pertambahan_nilai", "pajak pertambahan nilai", "required"],
            ["service_charge", "service charge", "required"],
            ["npwpd", "NPWPD", "required"],
            ["print_server_url", "URL print server", "required"],
        ]);

        OutletModel::create([
            "brand" => "PAK_USU",
            "name" => $this->input->post("name"),
            "address" => $this->input->post("address"),
            "phone" => $this->input->post("phone"),
            "pajak_pertambahan_nilai" => $this->input->post("pajak_pertambahan_nilai") / 100,
            "service_charge" => $this->input->post("service_charge") / 100,
            "npwpd" => $this->input->post("npwpd"),
            "print_server_url" => $this->input->post("print_server_url"),
        ]);

        $this->session->set_flashdata('message-success', 'Data berhasil ditambahkan.');
        redirect(base_url("outlet/index"));
    }

    public function edit($outlet_id)
    {
        OutletPolicy::canUpdate(Auth::user()) ?: $this->error403();

        $outlet = OutletModel::find($outlet_id) ?: $this->error404();
        $this->template->render("outlet/edit", compact("outlet"));
    }

    public function update($outlet_id)
    {
        $outlet = OutletModel::find($outlet_id) ?: $this->error404();
        OutletPolicy::canCreate(Auth::user()) ?: $this->error403();

        $this->validate([
            ["name", "nama", "required"],
            ["address", "alamat", "required"],
            ["phone", "nomor telefon", "required"],
            ["pajak_pertambahan_nilai", "pajak pertambahan nilai", "required"],
            ["service_charge", "service charge", "required"],
            ["npwpd", "NPWPD", "required"],
            ["print_server_url", "URL print server", "required"],
        ]);

        $outlet->update([
            "name" => $this->input->post("name"),
            "address" => $this->input->post("address"),
            "phone" => $this->input->post("phone"),
            "pajak_pertambahan_nilai" => $this->input->post("pajak_pertambahan_nilai") / 100,
            "service_charge" => $this->input->post("service_charge") / 100,
            "npwpd" => $this->input->post("npwpd"),
            "print_server_url" => $this->input->post("print_server_url"),
        ]);
        
        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function delete($outlet_id)
    {
        $outlet = OutletModel::find($outlet_id) ?: $this->error404();
        OutletPolicy::canDelete(Auth::user(), $outlet) ?: $this->error403();

        $outlet->delete();
        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');
        $this->redirectBack();
    }
}