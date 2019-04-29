<?php

use App\BaseController;
use App\Helpers\Auth;
use App\Policies\ReceiptPrinterPolicy;
use App\EloquentModels\ReceiptPrinter as ReceiptPrinterModel;

class ReceiptPrinter extends BaseController
{
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
        ReceiptPrinterPolicy::canIndex(Auth::user()) ?: $this->error403();

        $outlet = Auth::user()->outlet ?: $this->error403();
        $receipt_printers = $outlet->receipt_printers;

        $print_server_url = "http://localhost:8002/print";
        $this->template->render("receipt_printer/index", compact("receipt_printers", "print_server_url"));
    }
    
    public function edit($receipt_printer_id)
    {
        $receipt_printer = ReceiptPrinterModel::find($receipt_printer_id)
            ?: $this->error404();

        $this->template->render("receipt_printer/edit", compact("receipt_printer"));
    }

    public function update($receipt_printer_id)
    {
        $receipt_printer = ReceiptPrinterModel::find($receipt_printer_id)
            ?: $this->error404();

        $this->validate([
            ["name", "nama printer", "required"],
            ["ipv4_address", "alamat IP", "required"],
            ["port", "port", "required"],
        ]);

        $receipt_printer->update([
            "name" => $this->input->post("name"),
            "ipv4_address" => $this->input->post("ipv4_address"),
            "port" => $this->input->post("port"),
        ]);

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        redirect(base_url("receiptPrinter/edit/{$receipt_printer_id}"));
    }
}
