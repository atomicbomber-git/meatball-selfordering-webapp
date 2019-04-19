<?php ?>

<?php $this->layout('shared/base-guest', ["title" => "Daftar Menu"]) ?>

<div style="margin: 0px 100px 0px 100px">
    <div id="app">
        <home
            submit_url="<?= base_url('salesInvoice/store') ?>"
            :menu_data='<?= json_encode($menu_data) ?>'
            />
    </div>
</div>