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
                <a href="<?= base_url("outletDiscount/index") ?>">
                    Diskon
                </a>
            </li>
            <li class="breadcrumb-item active">
                '<?= $outlet->name ?>'
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <div class="d-flex justify-content-end">
        <a class="btn btn-dark btn-sm my-2" href="<?= base_url("discount/create/{$outlet->id}") ?>">
            Tambah Diskon Baru
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <h3 class="mb-4">
        <i class="fa fa-percent"></i>
        Detail Diskon Outlet '<?= $outlet->name ?>'
    </h3>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama Program Diskon </th>
                        <th> Waktu Mulai Berlaku </th>
                        <th> Waktu Selesai Berlaku </th>
                        <th> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($outlet->discounts as $key => $discount): ?>
                    <tr>
                        <td> <?= $key + 1 ?>. </td>
                        <td> <?= $discount->name ?> </td>
                        <td> <?= Formatter::datetime($discount->starts_at) ?> </td>
                        <td> <?= Formatter::datetime($discount->ends_at) ?> </td>
                        <td>
                            <a class="btn btn-dark btn-sm" href="<?= base_url("discount/edit/{$discount->id}") ?>">
                                Detail / Ubah
                            </a>

                            <form class="d-inline-block confirmed" method="POST" action="<?= base_url("discount/delete/{$discount->id}") ?>" >
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