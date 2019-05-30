<?php
    use App\Helpers\DefaultRoute;
    use App\Helpers\AppInfo;
use App\Helpers\Formatter;

?>

<?php $this->layout("shared/base", ["title" => "Transaksi"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>">
                    <?= AppInfo::name() ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <a href="<?= base_url("salesInvoice/index") ?>">
                    Transaksi
                </a>
            </li>
        </ol>
    </nav>

    <h3 class="m-b:3">
        <i class="fa fa-usd"></i>
        Transaksi / Kasir
    </h3>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-block">
            <table class="datatable table table-sm table-striped table-bordered t-a:c">
                <thead class="thead thead-dark">
                    <tr>
                        <th> Nomor Pesanan </th>
                        <th> Waktu </th>
                        <th class="t-a:c"> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($sales_invoices as $sales_invoice): ?>
                    <tr>
                        <td> <?= Formatter::salesInvoiceNumber($sales_invoice->number) ?> </td>
                        <td> <?= $sales_invoice->created_at ?> </td>
                        <td class="t-a:c">
                            <a href="<?= base_url("salesInvoice/confirm/{$sales_invoice->id}") ?>" class="btn btn-info btn-sm">
                                Konfirmasi
                            </a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->start("extra-scripts") ?>
    <script>
        $("table.datatable").DataTable({
            "language": { "url": "<?= base_url("assets/indonesian-datatables.json") ?>" },
            "order": [[0, "desc"]],
        })
    </script>
<?php $this->stop() ?>