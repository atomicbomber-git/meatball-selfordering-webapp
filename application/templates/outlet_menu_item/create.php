<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Helpers\Formatter;
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
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletMenu/index") ?>">
                    Menu Outlet
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletMenu/detail/{$outlet->id}") ?>">
                    '<?= $outlet->name ?>'
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletMenuItem/index/{$outlet->id}/{$menu_category->id}") ?>">
                    '<?= $menu_category->name ?>'
                </a>
            </li>
            <li class="breadcrumb-item">
                Tambahkan Menu
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3>
        <i class="fa fa-cutlery"></i>
        Tambahkan Menu Outlet '<?= $outlet->name ?>' <br/>
        Kategori '<?= $menu_category->name ?>'
    </h3>

    <div id="app">
        <outlet-menu-item-create
            submit_url="<?= base_url("outletMenuItem/store/{$outlet->id}") ?>"
            redirect_url="<?= base_url("outletMenuItem/index/{$outlet->id}/{$menu_category->id}") ?>"
            :menu_category='<?= json_encode($menu_category) ?>'
            />
    </div>
</div>