<?php

use App\BaseController;
use App\Helpers\Auth;
use App\EloquentModels\ReceiptPrinter as ReceiptPrinterModel;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

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
        $this->template->render("receipt_printer/index", compact("receipt_printers"));
    }

    public function test($receipt_printer_id)
    {
        $receipt_printer = ReceiptPrinterModel::find($receipt_printer_id) ?: $this->error404();
        $printer = new Printer(new NetworkPrintConnector($receipt_printer->ipv4_address, $receipt_printer->port));

        try {
            $printer->text("\n");
            $printer->text("PENGUJIAN PRINTER {$receipt_printer->name}\n");
            $printer->text("\n");
        }
        catch(\Exception $e) {

        }
        finally {
            $printer->close();
        };

        $this->redirectBack();
    }
}