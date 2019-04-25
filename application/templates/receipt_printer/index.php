<?php ?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

<div class="container">
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