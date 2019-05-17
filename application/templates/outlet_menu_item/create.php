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
                '<?= $outlet->name ?>'
            </li>
            <li class="breadcrumb-item">
                '<?= $menu_category->name ?>'
            </li>
            <li class="breadcrumb-item">
                Tambahkan Menu
            </li>
        </ol>
    </nav>


    <?php $this->insert("shared/message") ?>

    <h3>
        <i class="fa fa-cutlery"></i>
        Detail Menu Outlet '<?= $outlet->name ?>' <br/>
        Kategori '<?= $menu_category->name ?>'
    </h3>

    
</div>