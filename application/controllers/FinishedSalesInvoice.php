<?php

use App\BaseController;
use App\EloquentModels\SalesInvoice;

class FinishedSalesInvoice extends BaseController {
    protected function allowedMethods()
    {
        return [
            'show' => ['get'],
        ];
    }

    public function show($sales_invoice_id)
    {
        $sales_invoice = SalesInvoice::find($sales_invoice_id) ?: $this->error404();
        $this->jsonResponse($sales_invoice);
    }
}