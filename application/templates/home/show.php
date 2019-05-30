<?php ?>

<?php $this->layout('shared/base-guest', ["title" => "Daftar Menu"]) ?>

<div class="container-fluid">
    <div id="app">
        <home
            submit_url="<?= base_url('salesInvoice/store') ?>"
            :menu_data='<?= json_encode($menu_data) ?>'
            :outlet='<?= json_encode($outlet) ?>'
            >
        </home>
    </div>
</div>