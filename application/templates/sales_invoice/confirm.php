<?php $this->layout("shared/base", ["title" => "Konfirmasi Penjualan"]) ?>

<div class="container">
    <?php $this->insert("shared/message") ?>

    <h1 class="mb-1"> Konfirmasi Penjualan </h1>
    <hr class="mt-0"/>

    <div class="card">
        <div class="card-body">
            <div id="app">
                <sales-invoice-confirm
                    :outlet_menu_items='<?= json_encode($outlet_menu_items) ?>'
                    :sales_invoice='<?= json_encode($sales_invoice) ?>'
                    />
            </div>
        </div>
    </div>
</div>