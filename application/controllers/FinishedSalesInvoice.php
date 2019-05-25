<?php

use App\BaseController;
use App\EloquentModels\SalesInvoice;
use Yajra\DataTables\DataTables;

class FinishedSalesInvoice extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
        ];
    }

    public function index()
    {
        $sales_invoices = SalesInvoice::query()
            ->isFinished()
            ->orderByDesc("created_at")
            ->get();

        $this->template->render("finished_sales_invoice/index", compact("sales_invoices"));
    }
}