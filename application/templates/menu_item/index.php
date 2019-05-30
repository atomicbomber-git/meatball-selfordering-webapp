<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Policies\MenuItemPolicy;
use App\Helpers\Auth;

?>

<?php $this->layout("shared/base", ["title" => "Menu"]) ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">
            <a href="<?= base_url(DefaultRoute::get()) ?>">
                <?= AppInfo::name() ?>
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?= base_url("menuCategory/index") ?>">
                Kategori Menu
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Menu
        </li>
    </ol>
</nav>

<h3 class="m-b:3">
    <i class="fa fa-list"></i>
    Detail Kategori Menu '<?= $menu_category->name ?>'
</h3>

<div class="t-a:r m-y:3">
    <a href="<?= base_url("menuItem/create/{$menu_category->id}") ?>" class="btn btn-info">
        Tambahkan Menu Baru
    </a>
</div>

<?php $this->insert("shared/message") ?>

<div class="card">
    <div class="card-block">
        <table class="table table-sm table-bordered table-striped">
            <thead class="thead thead-dark">
                <tr>
                    <th> # </th>
                    <th> Nama </th>
                    <th class="t-a:c"> Kendali </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($menu_category->menu_items as $key => $menu_item) : ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $menu_item->name ?> </td>
                        <td class="t-a:c">
                            <form class="confirmed d-inline-block" method="POST" action="<?= base_url("menuItem/delete/{$menu_item->id}") ?>">
                                <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">

                                <a class="btn btn-info btn-sm" href="<?= base_url("menuItem/edit/{$menu_item->id}") ?>">
                                    Ubah
                                </a>

                                <button <?= MenuItemPolicy::canDelete(Auth::user(), $menu_item) ? '' : 'disabled' ?> class="btn btn-danger btn-sm">
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