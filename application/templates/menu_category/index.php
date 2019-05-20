<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Policies\MenuCategoryPolicy;
use App\Helpers\Auth;
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
            <li class="breadcrumb-item active" aria-current="page">
                Kategori Menu
            </li>
        </ol>
    </nav>

    <h3 class="mb-3">
        <i class="fa fa-list"></i>
        Kategori Menu
    </h3>

    <div class="text-right my-3">
        <a href="<?= base_url('menuCategory/create') ?>" class="btn btn-dark">
            Tambahkan Kategori Menu Baru
        </a>
    </div>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama </th>
                        <th class="text-center"> Gambar </th>
                        <th class="text-center"> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($menu_categories as $key => $menu_category) : ?>
                        <tr>
                            <td> <?= $key + 1 ?> </td>
                            <td> <?= $menu_category->name ?> </td>
                            <td class="text-center">
                                <img style="width: 320px; height: 240px; object-fit: cover" src="<?= base_url("menuCategory/image/{$menu_category->id}") ?>" alt="<?= $menu_category->name ?>">
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url("menuCategory/edit/{$menu_category->id}") ?>" class="btn btn-sm btn-dark">
                                    Ubah
                                </a>

                                <a class="btn btn-dark btn-sm" href="<?= base_url("menuItem/index/{$menu_category->id}") ?>">
                                    Detail
                                </a>

                                <form class="d-inline-block confirmed" method="POST" action="<?= base_url("menuCategory/delete/{$menu_category->id}") ?>">
                                    <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">

                                    <button <?= MenuCategoryPolicy::canDelete(Auth::user(), $menu_category) ? '' : 'disabled' ?> class="btn btn-sm btn-danger">
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