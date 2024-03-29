<?php

namespace App;

use App\Traits\HandlesErrors;
use App\Traits\HasReturnHelpers;
use App\Helpers\Auth;

class BaseController extends \CI_Controller
{
    use HandlesErrors, HasReturnHelpers;

    public function __construct()
    {
        parent::__construct();

        // Load libraries
        $this->load->library('session');
        $this->load->library('form_validation');
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

    public function runValidation($rules)
    {
        foreach ($rules as $rule) {

            if (strpos($rule[0], "*")) {
                $field = explode('[', $rule[0])[0];
                $data = $this->input->get_post($field);

                if (null === $data) {
                    continue;
                }

                $rule_template = $rule[0];

                foreach (array_keys($data) as $key) {
                    $rule[0] = str_replace("*", $key, $rule_template);
                    $this->form_validation->set_rules(...$rule);
                }
            }
            else {
                $this->form_validation->set_rules(...$rule);
            }
        }

        $this->form_validation->run();
        return $this->form_validation->error_array();
    }

    public function validate($rules)
    {
        $errors = $this->runValidation($rules);

        if ($errors) {
            if (! $this->input->is_ajax_request()) {
                $this->session->set_flashdata('errors', $errors);
                $this->saveOldFormData();
                $this->redirectBack();
            }
            else {
                http_response_code(422);
                $this->jsonResponse(['data' => [
                    'message' => 'Terdapat kesalahan dalam data.',
                    'errors' => $errors,
                ]]);
            }
        }
    }

    protected function saveOldFormData()
    {
        $this->session->set_flashdata('old', $this->input->get_post(null));
    }

    protected function redirectBack()
    {
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
        exit;
    }

    public function authorize()
    {
        if (!Auth::check()) {
            redirect(base_url("login"));
        }
    }
}
