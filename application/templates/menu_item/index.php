<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Policies\MenuItemPolicy;
use App\Helpers\Auth;

?>

<?php $this->layout("shared/base", ["title" => "Menu"]) ?>

<div class="container">
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

    <div class="text-right my-3">
        <a href="<?= base_url("menuItem/create/{$menu_category->id}") ?>" class="btn btn-dark">
            Tambahkan Menu Baru
        </a>
    </div>

    <?php $this->insert("shared/message") ?>

    <h3 class="mb-3">
        <i class="fa fa-list"></i>
        Detail Kategori Menu '<?= $menu_category->name ?>'
    </h3>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama </th>
                        <th class="text-center"> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($menu_category->menu_items as $key => $menu_item) : ?>
                        <tr>
                            <td> <?= $key + 1 ?> </td>
                            <td> <?= $menu_item->name ?> </td>
                            <td class="text-center">
                                <form class="confirmed d-inline-block" method="POST" action="<?= base_url("menuItem/delete/{$menu_item->id}") ?>">
                                    <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">

                                    <a class="btn btn-dark btn-sm" href="<?= base_url("menuItem/edit/{$menu_item->id}") ?>">
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
    </div>
</div>