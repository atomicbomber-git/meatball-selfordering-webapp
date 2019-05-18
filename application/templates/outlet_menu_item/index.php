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
                '<?= $menu_category->name ?>'
            </li>
        </ol>
    </nav>

    <h3 class="mb-3">
        <i class="fa fa-cutlery"></i>
        Detail Menu Outlet '<?= $outlet->name ?>'
        Kategori '<?= $menu_category->name ?>'
    </h3>

    <div class="text-right my-3">
        <a href="<?= base_url("outletMenuItem/create/{$outlet->id}/{$menu_category->id}") ?>" class="btn btn-dark">
            Tambahkan Menu
        </a>
    </div>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Menu </th>
                        <th class="text-right"> Harga (Rp.) </th>
                        <th class="text-center"> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($outlet_menu_items as $key => $outlet_menu_item): ?>
                    <tr>
                        <td> <?= $key + 1 ?>. </td>
                        <td> <?= $outlet_menu_item->menu_item->name ?> </td>
                        <td class="text-right"> <?= Formatter::currency($outlet_menu_item->price) ?> </td>
                        <td class="text-center">
                            <a
                                href="<?= base_url("outletMenuItem/edit/{$outlet_menu_item->id}") ?>"
                                class="btn btn-sm btn-dark">
                                Ubah
                            </a>
                            
                            <form
                                class="d-inline-block confirmed"
                                method="POST"
                                action="<?= base_url("outletMenuItem/delete/{$outlet_menu_item->id}") ?>"
                                >
                                <input type="hidden"
                                    name="<?= $this->csrf_name() ?>"
                                    value="<?= $this->csrf_token() ?>">
                                <button class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>