<?php

use App\BaseController;
use App\EloquentModels\Outlet;
use App\EloquentModels\SalesInvoice;
use App\Helpers\Date;

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
        $outlet = Outlet::find($outlet_id) ?:
            $this->error404();

        $filter_date = $this->input->get("filter_date") ??
            Date::today()->format("Y-m-d");

        $sales_invoices = SalesInvoice::query()
            ->select(
                "id", "number", "outlet_id", "waiter_id", "cashier_id",
                "created_at", "special_discount", "invoice_number"
            )
            ->where("outlet_id", $outlet->id)
            ->whereDate("created_at", $filter_date)
            ->with("waiter:id,name", "cashier:id,name")
            ->isFinished()
            ->orderByDesc("created_at")
            ->orderByDesc("number")
            ->get()
            ->map
            ->append("archived_special_discount", "archived_item_discount");

        $this->template
            ->render("outlet_finished_sales_invoice/detail", compact("sales_invoices", "outlet", "filter_date"));
    }
}