<?php
    use App\Helpers\DefaultRoute;
    use App\Helpers\Formatter;
    use App\Helpers\Date;
?>

<?php $this->start("extra-styles") ?>

<style>

td:nth-child(1), th:nth-child(1) {
    text-align: center !important;
}

td:nth-child(2),td:nth-child(3),td:nth-child(4),th:nth-child(2),th:nth-child(3),th:nth-child(4) {
    text-align: right !important;
}

</style>

<?php $this->stop() ?>

<?php $this->layout("shared/base", ["title" => "Histori Transaksi Outlet '{$outlet->name}' Tanggal " . Formatter::date(Date::today()) ]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletFinishedSalesInvoice/index") ?>">
                    Histori Transaksi
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
            '<?= $outlet->name ?>'
            </li>
        </ol>
    </nav>

    <h3 class="m-b:3">
        <i class="fa fa-book"></i>
        Histori Transaksi Outlet '<?= $outlet->name ?>'
        <p class="lead"> Tanggal <?= Formatter::date(Date::today()) ?> </p>
    </h3>

    <?php $this->insert("shared/message") ?>

    <div class="alert alert-dark">
        <div class="row">
            <div class="col-md-6">
                Histori Transaksi untuk Tanggal <strong> <?= Formatter::date(Date::today()) ?> </strong> 
            </div>

            <div class="col-md-6">
                <form class="form-inline justify-content-end" method="GET" action="">
                    <label for="filter_date" class=m-r:2> Tanggal: </label>
                    <input value="<?= $filter_date ?>" name="filter_date" class="form-control form-control-sm m-r:2" type="date">
                    <button class="btn btn-primary btn-sm"> Ubah Filter </button>
                </form>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-block">
            <div class="table-responsive">
                <table class="datatable table table-sm table-striped table-bordered">
                    <thead class="thead thead-dark">
                        <tr>
                            <th class="printable"> Nomor Invoice </th>
                            <th class="printable"> Total Pembayaran (Rp) </th>
                            <th class="printable"> Total Diskon Item (Rp) </th>
                            <th class="printable"> Total Diskon Khusus (Rp) </th>
                            <th class="t-a:c"> Kendali </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sales_invoices as $key => $sales_invoice): ?>
                        <tr>
                            <td class="t-a:c"> <?= Formatter::salesInvoiceId($sales_invoice->invoice_number) ?> </td>
                            <td> <?= Formatter::currency($sales_invoice->archived_rounding) ?> </td>
                            <td> <?= Formatter::currency($sales_invoice->archived_item_discount) ?> </td>
                            <td> <?= Formatter::currency($sales_invoice->archived_special_discount) ?> </td>
                            <td class="t-a:c">
                                <a
                                    class="btn btn-info btn-sm"
                                    href="<?= base_url("finishedSalesInvoice/show/{$sales_invoice->id}") ?>">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->start("extra-scripts") ?>
    <script>
        $("table.datatable").DataTable({
            ...window.datatable_config,

            "order": [[0, "desc"]],

            "footerCallback": function (row, data, start, end, display) {

                let total_paid_col = 1
                let total_item_discount_col = 2
                let total_special_discount_col = 3

                var api = this.api(), data;

                let total_paid = this.api()
                    .column(total_paid_col, { search: 'applied' })
                    .data()
                    .map(n => numeral(n).value())
                    .reduce((a, b) => {
                        return a + b
                    }, 0)

                let total_item_discount = this.api()
                    .column(total_item_discount_col, { search: 'applied' })
                    .data()
                    .map(n => numeral(n).value())
                    .reduce((a, b) => {
                        return a + b
                    }, 0)

                let total_special_discount = this.api()
                    .column(total_special_discount_col, { search: 'applied' })
                    .data()
                    .map(n => numeral(n).value())
                    .reduce((a, b) => {
                        return a + b
                    }, 0)

                $(api.column(total_paid_col).footer()).html(
                    currency_format(total_paid)
                );

                $(api.column(total_item_discount_col).footer()).html(
                    currency_format(total_item_discount)
                );

                $(api.column(total_special_discount_col).footer()).html(
                    currency_format(total_special_discount)
                );
            }
        })
    </script>
<?php $this->stop() ?>