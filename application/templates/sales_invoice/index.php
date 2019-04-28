<?php
use App\Helpers\DefaultRoute;

?>

<?php $this->layout("shared/base", ["title" => "Transaksi"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <a href="<?= base_url("salesInvoice/index") ?>">
                    Transaksi
                </a>
            </li>
        </ol>
    </nav>

    <h1 class="mb-1"> Transaksi / Kasir </h1>
    <hr class="mt-0"/>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> Nomor Pesanan </th>
                        <th> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($sales_invoices as $sales_invoice): ?>
                    <tr>
                        <td> <?= $sales_invoice->number ?> </td>
                        <td>
                            <a href="<?= base_url("salesInvoice/confirm/{$sales_invoice->id}") ?>" class="btn btn-dark btn-sm">
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