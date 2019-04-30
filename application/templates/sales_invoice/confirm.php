<?php
use App\Helpers\Formatter;

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
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h1 class="mb-1"> Konfirmasi Transaksi </h1>
    <hr class="mt-0"/>

    <div class="card">
        <div class="card-body" id="app">
            <sales-invoice-confirm
                :sales_invoice='<?= json_encode($sales_invoice) ?>'
                submit_url="<?= base_url("salesInvoice/processConfirm/{$sales_invoice->id}") ?>"
                redirect_url="<?= base_url("salesInvoice/index") ?>"
                >
            </sales-invoice-confirm>
        </div>
    </div>
</div>