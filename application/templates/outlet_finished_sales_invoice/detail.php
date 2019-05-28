<?php
    use App\Helpers\DefaultRoute;
    use App\Helpers\Formatter;
?>

<?php $this->layout("shared/base", ["title" => "Histori Transaksi Outlet '{$outlet->name}'"]) ?>

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

    <h3 class="mb-3">
        <i class="fa fa-book"></i>
        Histori Transaksi Outlet '<?= $outlet->name ?>'
    </h3>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="datatable table table-sm table-striped table-bordered">
                    <thead class="thead thead-dark">
                        <tr>
                            <th class="text-center printable"> Nomor Invoice </th>
                            <th class="text-right printable"> Total Pembayaran (Rp) </th>
                            <th class="text-right printable"> Total Diskon Item (Rp) </th>
                            <th class="text-right printable"> Total Diskon Khusus (Rp) </th>
                            <th class="text-center"> Kendali </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sales_invoices as $key => $sales_invoice): ?>
                        <tr>
                            <td class="text-center"> <?= Formatter::salesInvoiceId($sales_invoice->id) ?> </td>
                            <td class="text-right"> <?= Formatter::currency($sales_invoice->archived_rounding) ?> </td>
                            <td class="text-right"> <?= Formatter::currency($sales_invoice->archived_item_discount) ?> </td>
                            <td class="text-right"> <?= Formatter::currency($sales_invoice->archived_special_discount) ?> </td>
                            <td class="text-center">
                                <a
                                    class="btn btn-dark btn-sm"
                                    href="<?= base_url("finishedSalesInvoice/show/{$sales_invoice->id}") ?>">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->start("extra-scripts") ?>
    <script>
        $("table.datatable").DataTable({
            dom: '<<"row mb-4"<"col-sm-12 col-md-3"l><"col-sm-12 col-md-6 text-center"B><"col-sm-12 col-md-3"f>>rtip>',
            "language": { "url": "<?= base_url("assets/indonesian-datatables.json") ?>" },
            "order": [[0, "desc"]],

            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ".printable"
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: ".printable"
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ".printable"
                    }
                },
                'colvis'
            ]
        })
    </script>
<?php $this->stop() ?>