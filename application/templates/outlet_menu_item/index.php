<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Helpers\Formatter;
use App\Policies\OutletMenuItemPolicy;
use App\Helpers\Auth;

?>

<?php $this->layout("shared/base", ["title" => "Detail Menu Outlet"]) ?>

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

<h3 class="m-b:3">
    <i class="fa fa-cutlery"></i>
    Detail Menu Outlet '<?= $outlet->name ?>'
    Kategori '<?= $menu_category->name ?>'
</h3>

<div class="t-a:r m-y:3">
    <a href="<?= base_url("outletMenuItem/create/{$outlet->id}/{$menu_category->id}") ?>" class="btn btn-info">
        Tambahkan Menu
    </a>
</div>

<?php $this->insert("shared/message") ?>

<div class="card">
    <div class="card-block">
        <table class="table table-sm table-bordered table-striped">
            <thead class="thead thead-dark">
                <tr>
                    <th> # </th>
                    <th> Menu </th>
                    <th class="t-a:r"> Harga (Rp.) </th>
                    <th class="t-a:c"> Aktif / Non-Aktif </th>
                    <th class="t-a:c"> Prioritas </th>
                    <th class="t-a:c"> Kendali </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($outlet_menu_items as $key => $outlet_menu_item) : ?>
                    <tr>
                        <td> <?= $key + 1 ?>. </td>
                        <td> <?= $outlet_menu_item->menu_item->name ?> </td>
                        <td class="t-a:r"> <?= Formatter::currency($outlet_menu_item->price) ?> </td>
                        <td class="t-a:c">
                            <?php $this->insert("shared/activation_status", ["is_active" => $outlet_menu_item->is_active]) ?>
                        </td>
                        <td class="t-a:c">
                            <?= $outlet_menu_item->priority ?>
                        </td>
                        <td class="t-a:c">
                            <a href="<?= base_url("outletMenuItem/edit/{$outlet_menu_item->id}") ?>" class="btn btn-sm btn-info">
                                Ubah
                            </a>

                            <?php if ($outlet_menu_item->is_active) : ?>

                                <form class="d-inline-block confirmed" method="POST" action="<?= base_url("outletMenuItem/deactivate/{$outlet_menu_item->id}") ?>">
                                    <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
                                    <button class="btn btn-warning btn-sm">
                                        Non-Aktifkan
                                    </button>
                                </form>

                            <?php else : ?>

                                <form class="d-inline-block confirmed" method="POST" action="<?= base_url("outletMenuItem/activate/{$outlet_menu_item->id}") ?>">
                                    <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
                                    <button class="btn btn-success btn-sm">
                                        Aktifkan
                                    </button>
                                </form>

                            <?php endif ?>

                            <form class="d-inline-block confirmed" method="POST" action="<?= base_url("outletMenuItem/delete/{$outlet_menu_item->id}") ?>">
                                <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
                                <button <?= OutletMenuItemPolicy::canDelete(Auth::user(), $outlet_menu_item) ? '' : 'disabled' ?> class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div