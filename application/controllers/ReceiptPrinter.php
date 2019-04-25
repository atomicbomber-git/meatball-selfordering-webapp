<?php

use App\BaseController;
use App\Helpers\Auth;
use App\EloquentModels\ReceiptPrinter as ReceiptPrinterModel;
use GuzzleHttp\Client as Guzzle;

class ReceiptPrinter extends BaseController {
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
            'test' => ['post'],
        ];
    }

    public function index()
    {
        $outlet = Auth::user()->outlet ?: $this->error403();
        $receipt_printers = $outlet->receipt_printers;

        $print_server_url = "http://localhost:8002/print";
        $this->template->render("receipt_printer/index", compact("receipt_printers", "print_server_url"));
    }
}