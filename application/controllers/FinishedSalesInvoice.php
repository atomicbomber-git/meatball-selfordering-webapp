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
        $sales_invoice->load(
            "outlet:id,name",
            "waiter:id,name",
            "cashier:id,name",
            "sales_invoice_items",
        );

        $sales_invoice->append("archived_rounding", "archived_prediscount_total_price", "archived_tax", "archived_service_charge");
        $this->template->render("finished_sales_invoice/show", compact("sales_invoice"));
    }
}