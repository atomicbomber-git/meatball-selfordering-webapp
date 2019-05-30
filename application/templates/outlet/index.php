<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Policies\OutletPolicy;
use App\Helpers\Auth;

?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

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

<h3 class="m-b:3">
    <i class="fa fa-building"></i>
    Outlet
</h3>

<div class="t-a:r m-y:3">
    <a href="<?= base_url("outlet/create") ?>" class="btn btn-info">
        Tambahkan Outlet Baru
    </a>
</div>

<div class="card">
    <div class="card-block">
        <table class="datatable table table-sm table-bordered table-striped">
            <thead class="thead thead-dark">
                <tr>
                    <th> # </th>
                    <th> Nama </th>
                    <th> Alamat </th>
                    <th class="t-a:c"> Kendali </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($outlets as $key => $outlet) : ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $outlet->name ?> </td>
                        <td> <?= $outlet->address ?> </td>
                        <td class="t-a:c">
                            <a class="btn btn-info btn-sm" href="<?= base_url("outlet/edit/{$outlet->id}") ?>">
                                Ubah
                            </a>

                            <form class="d-inline-block confirmed" method="POST" action="<?= base_url("outlet/delete/{$outlet->id}") ?>">
                                <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
                                <button <?= OutletPolicy::canDelete(Auth::user(), $outlet) ?: 'disabled' ?> class="btn btn-danger btn-sm">
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

<?php $this->start("extra-scripts") ?>
    <?php $this->insert("shared/datatable") ?>
<?php $this->stop() ?>