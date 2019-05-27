<?php

use App\BaseController;
use App\Helpers\Auth;
use App\Policies\ReceiptPrinterPolicy;
use App\EloquentModels\ReceiptPrinter as ReceiptPrinterModel;
use Illuminate\Database\Capsule\Manager as DB;

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
            'activate' => ['get'],
            'edit' => ['get'],
            'update' => ['post'],
            'delete' => ['post'],
            'test' => ['post'],
        ];
    }

    public function activate($receipt_printer_id)
    {
        $receipt_printer = ReceiptPrinterModel::find($receipt_printer_id) ?:
            $this->error404();
        $outlet = Auth::user()->outlet ?:
            $this->error403();
            

        DB::transaction(function() use($outlet, $receipt_printer) {
            /* Set other printers in the outlet of the same type to inactive */
            $outlet->receipt_printers()
                ->where("type", $receipt_printer->type)
                ->update(["is_active" => 0]);

            /* Set itself to active */
            $receipt_printer->update(["is_active" => 1]);
        });

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        $this->redirectBack();
    }

    public function index()
    {
        ReceiptPrinterPolicy::canIndex(Auth::user()) ?: $this->error403();

        $outlet = Auth::user()->outlet ?: $this->error403();
        $outlet->load(["receipt_printers" => function ($query) {
            $query->orderBy("type");
        }]);

        $receipt_printers = $outlet->receipt_printers;

        $print_server_url = $outlet->print_server_url;
        $this->template->render("receipt_printer/index", compact("receipt_printers", "print_server_url"));
    }

    public function create()
    {
        $this->template->render("receipt_printer/create");
    }

    public function store()
    {
        $this->validate([
            ["name", "nama printer", "required|is_unique[receipt_printers.name]"],
            ["ipv4_address", "alamat IP", "required"],
            ["port", "port", "required"],
            ["type", "port", "required"],
        ]);

        $outlet = Auth::user()->outlet ?: $this->error403();
        
        ReceiptPrinterModel::create([
            "outlet_id" => $outlet->id,
            "type" => $this->input->post("type"),
            "name" => $this->input->post("name"),
            "ipv4_address" => $this->input->post("ipv4_address"),
            "port" => $this->input->post("port"),
        ]);

        $this->session->set_flashdata('message-success', 'Data berhasil ditambahkan.');
        redirect(base_url("receiptPrinter/index"));
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
            ["type", "port", "required"],
        ]);

        $receipt_printer->update([
            "name" => $this->input->post("name"),
            "ipv4_address" => $this->input->post("ipv4_address"),
            "port" => $this->input->post("port"),
            "type" => $this->input->post("type"),
        ]);

        $this->session->set_flashdata('message-success', 'Data berhasil diperbarui.');
        redirect(base_url("receiptPrinter/edit/{$receipt_printer_id}"));
    }

    public function delete($receipt_printer_id)
    {
        $receipt_printer = ReceiptPrinterModel::find($receipt_printer_id)
            ?: $this->error404();

        $receipt_printer->delete();
        $this->session->set_flashdata('message-success', 'Data berhasil dihapus.');

        $this->jsonResponse([
            "redirect_url" => base_url("receiptPrinter/index")
        ]);
    }
}
