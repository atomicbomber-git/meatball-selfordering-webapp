<?php

use App\BaseController;
use App\EloquentModels\User;
use App\Helpers\DefaultRoute;
use App\Helpers\Auth;

class Login extends BaseController {
    public function __construct()
    {
        parent::__construct();
        Auth::check() && redirect(DefaultRoute::get());
    }

    protected function allowedMethods()
    {
        return [
            'show' => ['get'],
            'handle' => ['post'],
        ];
    }

    public function show()
    {
        $this->template->render("login/show");
    }

    public function handle()
    {
        $this->validate([
            ["username", "nama pengguna", "required"],
            ["password", "kata sandi", "required"],
        ]);

        $user = User::query()
            ->whereRaw("username = ?", [$this->input->post("username")])
            ->first();

        if (null === $user) {
            $this->session->set_flashdata('errors', ['authentication' => 'Maaf, identitas anda keliru.']);
            $this->redirectBack();
        }
    
        if (!password_verify($this->input->post('password'), $user->password)) {
            $this->session->set_flashdata('errors', ['authentication' => 'Maaf, identitas anda keliru.']);
            $this->redirectBack();
        }

        $this->session->user = $user;
        $this->authenticated();
    }

    public function authenticated()
    {
        redirect(DefaultRoute::get());
    }
}