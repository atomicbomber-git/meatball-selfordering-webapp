<?php
use App\Helpers\Formatter;
use function GuzzleHttp\json_encode;

$this->layout("shared/base", ["title" => "Konfirmasi Transaksi"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <a href="<?= base_url("salesInvoice/index") ?>">
                    Transaksi
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <a href="<?= base_url("salesInvoice/confirm/{$sales_invoice->id}") ?>">
                    Konfirmasi Transaksi
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Revisi Transaksi
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h1 class="mb-1"> Konfirmasi Transaksi </h1>
    <hr class="mt-0"/>

    <div class="card">
        <div class="card-body" id="app">
            <sales-invoice-update-and-confirm
                :sales_invoice='<?= json_encode($sales_invoice) ?>'
                :outlet_menu_items='<?= json_encode($outlet_menu_items) ?>'
                submit_url="<?= base_url("salesInvoice/processUpdateAndConfirm/{$sales_invoice->id}") ?>"
                redirect_url="<?= base_url("salesInvoice/index") ?>"
                >

            </sales-invoice-update-and-confirm>
        </div>
    </div>
</div>

<?php $this->start('extra-scripts') ?>

<script>
    $(document).ready(function() {
        $("form#confirm-sales-invoice").submit(function (e) {
            e.preventDefault()

            swal({
                icon: "warning",
                text: "Apakah Anda yakin Anda ingin mengkonfirmasi pesanan ini?s",
                buttons: ["Tidak", "Ya"],
            })
            .then(is_ok => {
                if (is_ok) {
                    $(this).off("submit").submit()
                }
            })

        });
    })
</script>

<?php $this->stop() ?>