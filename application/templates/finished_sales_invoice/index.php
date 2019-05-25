<?php
    use App\Helpers\DefaultRoute;
?>

<?php $this->layout("shared/base", ["title" => "Sejarah Transaksi"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <a href="<?= base_url("finishedSalesInvoice/index") ?>">
                    Sejarah Transaksi
                </a>
            </li>
        </ol>
    </nav>

    <h1 class="mb-1"> Sejarah Transaksi </h1>
    <hr class="mt-0"/>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead thead-dark">
                        <tr>
                            <th> # </th>
                            <th> Tanggal / Waktu </th>
                            <th> Kasir </th>
                            <th> Waiter / Service </th>
                            <th> Kendali </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sales_invoices as $key => $sales_invoice): ?>
                        <tr>
                            <td> <?= $key + 1 ?> </td>
                            <td> <?= $sales_invoice->created_at ?> </td>
                            <td> <?= $sales_invoice->cashier->name ?? '-' ?> </td>
                            <td> <?= $sales_invoice->waiter->name ?? '-' ?> </td>
                            <td>  </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->start("extra-scripts") ?>
    <?php $this->insert("shared/datatable") ?>
<?php $this->stop() ?>