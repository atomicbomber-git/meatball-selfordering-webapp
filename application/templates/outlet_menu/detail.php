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
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletMenu/index") ?>">
                    Menu Outlet
                </a>
            </li>
            <li class="breadcrumb-item active">
                '<?= $outlet->name ?>'
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3>
        Detail Menu Outlet
    </h3>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Kategori Menu </th>
                        <th> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($menu_categories as $menu_category): ?>
                    <tr>
                        <td> <?= $menu_category->id ?> </td>
                        <td> <?= $menu_category->name ?> </td>
                        <td>
                            <a class="btn btn-dark btn-sm" href="<?= base_url("outletMenuItem/index/{$outlet->id}/{$menu_category->id}") ?>">
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