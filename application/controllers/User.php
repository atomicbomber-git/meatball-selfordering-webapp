<?php

use App\BaseController;
use App\EloquentModels\User as UserModel;
use App\Validators\IsUniqueExcept;
use App\Policies\UserPolicy;
use App\Helpers\Auth;

class User extends BaseController {
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

    public function index()
    {
        UserPolicy::canIndex(Auth::user()) ?: $this->error403();

        $users = UserModel::query()
            ->withoutGlobalScope("active")
            ->select("id", "name", "username", "level", "is_active")
            ->orderByDesc("is_active")
            ->withCount(UserModel::RELATED_ENTITIES)
            ->get();

        $this->template->render("user/index", compact("users"));
    }

    public function activate($user_id)
    {
        $user = UserModel::findOrFail($user_id) ?:
            $this->error404();

        UserPolicy::canToggleActivationStatus(Auth::user(), $user) ?:
            $this->error403();

        $user->update(["is_active" => true]);

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function deactivate($user_id)
    {
        $user = UserModel::findOrFail($user_id) ?:
            $this->error404();

        UserPolicy::canToggleActivationStatus(Auth::user(), $user) ?:
            $this->error403();

        $user->update(["is_active" => false]);

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function create()
    {
        UserPolicy::canCreate(Auth::user()) ?: $this->error403();

        $this->template->render("user/create");
    }

    public function store()
    {
        UserPolicy::canCreate(Auth::user()) ?: $this->error403();

        $this->validate([
            ["name", "nama", "required"],
            ["username", "nama pengguna", "required|is_unique[users.username]"],
            ["password", "kata sandi", "required"],
            ["level", "hak akses", "required"],
        ]);

        UserModel::create([
            "name" => $this->input->post("name"),
            "username" => $this->input->post("username"),
            "password" => $this->input->post("password"),
            "level" => $this->input->post("level"),
        ]);

        $this->session->set_flashdata('message-success', 'Data berhasil ditambahkan.');
        redirect(base_url("user/index"));
    }

    public function edit($user_id)
    {
        UserPolicy::canUpdate(Auth::user()) ?: $this->error403();

        $user = UserModel::find($user_id) ?: $this->error404();
        $this->template->render('user/edit', compact('user'));
    }

    public function update($user_id)
    {
        UserPolicy::canUpdate(Auth::user()) ?: $this->error403();
        $user = UserModel::find($user_id) ?: $this->error404();

        $this->validate([
            ["name", "nama", "required"],
            ["username", "nama pengguna", ["required", IsUniqueExcept::validator("users.username", $user->username) ]],
            ["level", "hak akses", "required"],
        ]);

        $update_data = [
            "name" => $this->input->post("name"),
            "username" => $this->input->post("username"),
            "level" => $this->input->post("level"),
        ];

        if (!empty($this->input->post("password"))) {
            $update_data = array_merge($update_data, [
                "password" => password_hash($this->input->post("password"), PASSWORD_DEFAULT)
            ]);
        }

        $user->update($update_data);

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function delete($user_id)
    {
        $user = UserModel::find($user_id) ?: $this->error404();

        if (!UserPolicy::canDelete(Auth::user(), $user)) {
            $this->session->set_flashdata('message-success', 'Data tidak dapat dihapus.');
            $this->redirectBack();
        }
        
        $user->delete();

        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');
        $this->redirectBack();
    }
}