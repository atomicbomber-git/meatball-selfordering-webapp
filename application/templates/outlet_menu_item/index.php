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
        </ol>
    </nav>

    <div class="text-right my-3">
        <a href="<?= base_url("outletMenuItem/create/{$outlet->id}") ?>" class="btn btn-dark">
            Tambahkan Menu
        </a>
    </div>

    <?php $this->insert("shared/message") ?>

    <h3>
        <i class="fa fa-cutlery"></i>
        Detail Menu Outlet '<?= $outlet->name ?>' <br/>
        Kategori '<?= $menu_category->name ?>'
    </h3>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Menu </th>
                        <th class="text-right"> Harga (Rp.) </th>
                        <th> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($outlet_menu_items as $outlet_menu_item): ?>
                    <tr>
                        <td> <?= $outlet_menu_item->id ?> </td>
                        <td> <?= $outlet_menu_item->menu_item->name ?> </td>
                        <td class="text-right"> <?= Formatter::currency($outlet_menu_item->price) ?> </td>
                        <td>
                            <button class="btn btn-sm btn-dark">
                                Ubah
                            </button>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>