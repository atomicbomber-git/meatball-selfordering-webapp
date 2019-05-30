<?php
    use App\Helpers\DefaultRoute;
?>

<?php $this->layout("shared/base", ["title" => "Sejarah Transaksi"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Histori Transaksi
            </li>
        </ol>
    </nav>

    <h3 class="m-b:3">
        <i class="fa fa-book"></i>
        Histori Transaksi
    </h3>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-block">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama Outlet </th>
                        <th class="t-a:c"> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($outlets as $outlet): ?>
                    <tr>
                        <td> <?= $outlet->id ?> </td>
                        <td> <?= $outlet->name ?> </td>
                        <td class="t-a:c">
                            <a
                                href="<?= base_url("outletFinishedSalesInvoice/detail/{$outlet->id}") ?>"
                                class="btn btn-info btn-sm">
                                Detail
                            </a>
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