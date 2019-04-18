<?php

use App\BaseController;

class Home extends BaseController
{
    protected function allowedMethods()
    {
        return [
            'show' => ['get'],
        ];
    }

    public function show()
    {
        $this->template->render("home/show");
    }
}