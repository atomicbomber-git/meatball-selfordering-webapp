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
            <table class="table table-sm table-striped table-bordered">
                <thead class="thead thead-dark">
                    <tr>
                        <th> Created At </th>
                        <th> Updated At </th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->start('extra-scripts') ?>

<script>
    $(document).ready(function() {
        $("table").DataTable({
            "serverSide": true,
            "ajax": "<?= base_url('finishedSalesInvoice/index') ?>",
            "columns": [
                { "name": "created_at" },
                { "name": "updated_at" },
            ]
        });
    })
</script>

<?php $this->stop() ?>