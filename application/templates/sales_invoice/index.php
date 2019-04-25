<?php ?>

<?php $this->layout("shared/base", ["title" => "Penjualan"]) ?>

<div class="container">
    <?php $this->insert("shared/message") ?>

    <h1 class="mb-1"> Penjualan / Kasir </h1>
    <hr class="mt-0"/>

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