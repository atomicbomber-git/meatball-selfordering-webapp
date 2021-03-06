<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
?>

<?php $this->layout("shared/base", ["title" => "Ubah Diskon"]) ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">
            <a href="<?= base_url(DefaultRoute::get()) ?>">
                <?= AppInfo::name() ?>
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= base_url("outletDiscount/index") ?>">
                Diskon
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= base_url("outletDiscount/detail/{$discount->outlet->id}") ?>">
                '<?= $discount->outlet->name ?>'
            </a>
        </li>
        <li class="breadcrumb-item active">
            Ubah Diskon
        </li>
    </ol>
</nav>

<?php $this->insert("shared/message") ?>

<h3 class="m-b:4">
    <i class="fa fa-pencil"></i>
    Ubah Diskon
</h3>

<div id="app">
    <discount-edit submit_url="<?= base_url("discount/update/{$discount->id}") ?>" redirect_url="<?= base_url("discount/edit/{$discount->id}") ?>" :discount='<?= json_encode($discount) ?>' />
</div