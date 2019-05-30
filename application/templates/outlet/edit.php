<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">
            <a href="<?= base_url(DefaultRoute::get()) ?>">
                <?= AppInfo::name() ?>
            </a>
        </li>
        <li class="breadcrumb-item" aria-current="page">
            <a href="<?= base_url("outlet/index") ?>">
                Outlet
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Ubah Outlet '<?= $outlet->name ?>'
        </li>
    </ol>
</nav>

<?php $this->insert("shared/message") ?>

<h3 class="m-b:3">
    <i class="fa fa-building"></i>
    Ubah Outlet '<?= $outlet->name ?>'
</h3>

<div id="app">
    <outlet-edit submit_url="<?= base_url("outlet/update/{$outlet->id}") ?>" redirect_url="<?= base_url("outlet/edit/{$outlet->id}") ?>" :users='<?= json_encode($users) ?>' :outlet='<?= json_encode($outlet) ?>'>
    </outlet-edit>
</div