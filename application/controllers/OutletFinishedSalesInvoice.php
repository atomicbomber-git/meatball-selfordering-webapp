<?php

use App\BaseController;
use App\EloquentModels\Outlet;
use App\EloquentModels\SalesInvoice;

class OutletFinishedSalesInvoice extends BaseController {
    protected function allowedMethods()
    {
        return [
            'index' => ['get'],
            'detail' => ['get'],
        ];
    }

    public function index()
    {
        $outlets = Outlet::select("id", "name")->get();
        $this->template->render("outlet_finished_sales_invoice/index", compact("outlets"));
    }

    public function detail($outlet_id)
    {
        $outlet = Outlet::find($outlet_id) ?: $this->error404();

        $sales_invoices = SalesInvoice::query()
            ->where("outlet_id", $outlet->id)
            ->with("waiter", "cashier")
            ->isFinished()
            ->orderByDesc("created_at")
            ->get();

        $this->template->render("outlet_finished_sales_invoice/detail", compact("sales_invoices", "outlet"));
    }
}