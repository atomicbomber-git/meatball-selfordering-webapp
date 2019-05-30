<?php

use App\BaseController;
use App\EloquentModels\Outlet as OutletModel;
use App\Policies\OutletPolicy;
use App\Helpers\Auth;
use App\EloquentModels\User;
use Illuminate\Database\Capsule\Manager as DB;
use App\EloquentModels\OutletUser;

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

        $users = User::query()
            ->select("id", "level", "name")
            ->isNotAdmin()
            ->whereDoesntHave("outlet_user")
            ->whereDoesntHave("supervised_outlet")
            ->get();

        $this->template->render("outlet/create", compact("users"));
    }

    public function store()
    {
        OutletPolicy::canCreate(Auth::user()) ?: $this->error403();

        $this->validate([
            ["name", "nama", ["required", "is_unique[outlets.name]"]],
            ["address", "alamat", "required"],
            ["phone", "nomor telefon", "required"],
            ["pajak_pertambahan_nilai", "pajak pertambahan nilai", "required"],
            ["service_charge", "service charge", "required"],
            ["npwpd", "NPWPD", "required"],
            ["print_server_url", "URL print server", "required"],
            ["supervisor_id", "supervisor", "required"],
        ]);

        DB::transaction(function() {
            $outlet = OutletModel::create([
                "brand" => "PAK_USU",
                "name" => $this->input->post("name"),
                "address" => $this->input->post("address"),
                "phone" => $this->input->post("phone"),
                "pajak_pertambahan_nilai" => $this->input->post("pajak_pertambahan_nilai") / 100,
                "service_charge" => $this->input->post("service_charge") / 100,
                "npwpd" => $this->input->post("npwpd"),
                "print_server_url" => $this->input->post("print_server_url"),
                "supervisor_id" => $this->input->post("supervisor_id"),
            ]);

            foreach ($this->input->post("outlet_users") as $user_id) {
                OutletUser::create([
                    "user_id" => $user_id,
                    "outlet_id" => $outlet->id,
                ]);
            }
        });

        $this->session->set_flashdata('message-success', 'Data berhasil ditambahkan.');
    }

    public function edit($outlet_id)
    {
        OutletPolicy::canUpdate(Auth::user()) ?: $this->error403();
        $outlet = OutletModel::find($outlet_id) ?: $this->error404();

        $outlet->load("supervisor:id,name,level", "outlet_users:id,outlet_id,user_id");
        $users = User::query()
            ->select("id", "level", "name")
            ->isNotAdmin()
            ->get();

        $this->template->render("outlet/edit", compact("outlet", "users"));
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
            ["supervisor_id", "supervisor", "required"],
        ]);

        DB::transaction(function() use($outlet) {
            $outlet->update([
                "name" => $this->input->post("name"),
                "address" => $this->input->post("address"),
                "phone" => $this->input->post("phone"),
                "pajak_pertambahan_nilai" => $this->input->post("pajak_pertambahan_nilai") / 100,
                "service_charge" => $this->input->post("service_charge") / 100,
                "npwpd" => $this->input->post("npwpd"),
                "print_server_url" => $this->input->post("print_server_url"),
                "supervisor_id" => $this->input->post("supervisor_id"),
            ]);

            $outlet->outlet_users()->delete();

            foreach ($this->input->post("outlet_users") as $user_id) {
                OutletUser::create([
                    "user_id" => $user_id,
                    "outlet_id" => $outlet->id,
                ]);
            }
        });

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function delete($outlet_id)
    {
        $outlet = OutletModel::find($outlet_id) ?: $this->error404();
        OutletPolicy::canDelete(Auth::user(), $outlet) ?: $this->error403();

        DB::transaction(function() use($outlet) {
            $outlet->outlet_users()->delete();
            $outlet->delete();
        });

        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');
        $this->redirectBack();
    }
}