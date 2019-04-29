<?php
    use App\Helpers\DefaultRoute;
?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Printer
            </li>
        </ol>
    </nav>

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