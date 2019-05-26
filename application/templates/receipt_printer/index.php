<?php
    use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;

?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>"> <?= AppInfo::name() ?> </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Printer
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3 class="mb-3">
        <i class="fa fa-print"></i>
        Printer
    </h3>

    <div class="text-right my-3">
        <a href="<?= base_url("receiptPrinter/create") ?>" class="btn btn-dark">
            Tambahkan Printer Baru
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="app">
                <receipt-printer-index
                    print_server_url="<?= $print_server_url ?>"
                    :receipt_printers='<?= json_encode($receipt_printers) ?>'
                    />
            </div>
        </div>
    </div>
</div>