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
            <li class="breadcrumb-item active" aria-current="page">
                Outlet
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3 class="mb-3">
        <i class="fa fa-building"></i>
        Outlet
    </h3>

    <div class="d-flex justify-content-end my-3">
        <a href="<?= base_url("outlet/create") ?>" class="btn btn-dark">
            Tambahkan Outlet Baru
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama </th>
                        <th> Alamat </th>
                        <th class="text-center"> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($outlets as $key => $outlet): ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $outlet->name ?> </td>
                        <td> <?= $outlet->address ?> </td>
                        <td class="text-center">
                            <a class="btn btn-dark btn-sm" href="<?= base_url("outlet/edit/{$outlet->id}") ?>">
                                Ubah
                            </a>

                            <form class="d-inline-block" method="POST" action="<?= base_url("outlet/delete/{$outlet->id}") ?>" >
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