<?php
    use App\Helpers\DefaultRoute;
    use App\Helpers\AppInfo;
    use App\Helpers\Formatter;
    use App\Policies\DiscountPolicy;
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

    <h3 class=m-b:4>
        <i class="fa fa-percent"></i>
        Detail Diskon Outlet '<?= $outlet->name ?>'
    </h3>

    <div class="t-a:r">
        <a class="btn btn-info m-y:2" href="<?= base_url("discount/create/{$outlet->id}") ?>">
            Tambah Diskon Baru
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-block">
            <table class="datatable table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama Program Diskon </th>
                        <th> Waktu Mulai Berlaku </th>
                        <th> Waktu Selesai Berlaku </th>
                        <th class="t-a:c"> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($outlet->discounts as $key => $discount): ?>
                    <tr>
                        <td> <?= $key + 1 ?>. </td>
                        <td> <?= $discount->name ?> </td>
                        <td> <?= Formatter::datetime($discount->starts_at) ?> </td>
                        <td> <?= Formatter::datetime($discount->ends_at) ?> </td>
                        <td class="t-a:c">
                            <a class="btn btn-info btn-sm" href="<?= base_url("discount/edit/{$discount->id}") ?>">
                                Detail / Ubah
                            </a>

                            <form class="d-inline-block confirmed" method="POST" action="<?= base_url("discount/delete/{$discount->id}") ?>" >
                                <input type="hidden"
                                    name="<?= $this->csrf_name() ?>"
                                    value="<?= $this->csrf_token() ?>">

                                <button
                                    <?= DiscountPolicy::canDelete(Auth::user(), $discount) ? '' : 'disabled' ?>
                                    class="btn btn-danger btn-sm">
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

<?php $this->start("extra-scripts") ?>
    <?php $this->insert("shared/datatable") ?>
<?php $this->stop() ?>