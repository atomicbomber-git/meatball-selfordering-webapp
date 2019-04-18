<?php

namespace App;

use App\Traits\HandlesErrors;
use App\Traits\HasReturnHelpers;

class BaseController extends \CI_Controller
{
    use HandlesErrors, HasReturnHelpers;

    public function __construct()
    {
        parent::__construct();

        // Load libraries
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();

        $this->load->library("eloquent");
        $this->load->library("template");
    }

    public function _remap($method, $args)
    {
        if (! isset($this->allowedMethods()[$method])) {
            $this->error403();
            exit;
        }

        if (! in_array($this->input->method(), $this->allowedMethods()[$method])) {
            $this->error403();
            exit;
        }

        $this->{$method}(...$args);
    }
}
