<?php ?>

<?php $this->layout('shared/base-guest', ["title" => "Daftar Menu"]) ?>

<div style="margin: 0px 200px 0px 200px">
    <div id="app">
        <home
            submit_url="<?= base_url('salesInvoice/store') ?>"
            :menu_data='<?= json_encode($menu_data) ?>'
            />
    </div>
</div>