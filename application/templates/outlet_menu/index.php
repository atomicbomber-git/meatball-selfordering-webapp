<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
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
            Menu Outlet
        </li>
    </ol>
</nav>

<?php $this->insert("shared/message") ?>

<h3 class="m-b:3">
    <i class="fa fa-cutlery"></i>
    Menu Outlet
</h3>

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
                <?php foreach ($outlets as $outlet) : ?>
                    <tr>
                        <td> <?= $outlet->id ?> </td>
                        <td> <?= $outlet->name ?> </td>
                        <td class="t-a:c">
                            <a href="<?= base_url("outletMenu/detail/{$outlet->id}") ?>" class="btn btn-info btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div