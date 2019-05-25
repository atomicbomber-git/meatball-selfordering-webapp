<?php
    use App\Helpers\DefaultRoute;
    use App\Helpers\AppInfo;
?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

<div class="container">
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
                Tambahkan Outlet Baru
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3 class="mb-3">
        <i class="fa fa-building"></i>
        Tambahkan Outlet Baru
    </h3>

    <div id="app" class="mb-5">
        <outlet-create
            submit_url="<?= base_url("outlet/store") ?>"
            redirect_url="<?= base_url("outlet/index") ?>"
            :users='<?= json_encode($users) ?>'
            >
        </outlet-create>
    </div>
</div>