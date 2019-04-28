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
        
        if ($this->input->is_ajax_request()) {

            $main_query = SalesInvoice::query()
                ->where("status", SalesInvoice::FINISHED);
            $total = $main_query->count();

            $columns = collect($this->input->get("columns"))->pluck("name");
            $search_value = $this->input->get_post("search")["value"];
            $filtered_query = $main_query->when($search_value, function ($query) use($columns, $search_value) {
                $query->where(function ($query) use ($columns, $search_value) {
                    foreach ($columns as $column) {
                        $query->orWhere($column, "like", "%{$search_value}%");
                    }
                });
            });

            $filtered = $filtered_query->count();

            $data = $filtered_query
                ->select(...$columns)
                ->offset($this->input->get_post("start"))
                ->limit($this->input->get_post("length"))
                ->get()
                ->map(function ($sales_invoice) {
                    return array_values($sales_invoice->toArray());
                })
                ->toArray();

            $this->jsonResponse([
                "draw" => (int) $this->input->get_post("draw"),
                "recordsTotal" => $total,
                "recordsFiltered" => $filtered,
                "data" => $data,
            ]);
        }

        $this->template->render("finished_sales_invoice/index");
    }
}