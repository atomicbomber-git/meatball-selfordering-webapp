<?php ?>

<?php $this->layout('shared/base-guest', ["title" => "Menu"]) ?>

<?php $this->start("extra-styles") ?>

<style>
    body {
        background-image: url("/assets/header.jpg")
    }
</style>

<?php $this->stop() ?>

<div class="container-fluid" style="margin-top: 300px">
    <div id="app">
        <home
            submit_url="<?= base_url('salesInvoice/store') ?>"
            :menu_data='<?= json_encode($menu_data) ?>'
            :outlet='<?= json_encode($outlet) ?>'
            >
        </home>
    </div>
</div>