<?php

use App\BaseController;
use App\EloquentModels\User;

class Logout extends BaseController {
    protected function allowedMethods()
    {
        return [
            'handle' => ['post'],
        ];
    }

    public function handle()
    {
        $this->session->user = null;
        redirect("login");
    }
}