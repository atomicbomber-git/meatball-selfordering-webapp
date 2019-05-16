<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Helpers\Formatter;
use function GuzzleHttp\json_encode;

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
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletDiscount/detail/{$outlet->id}") ?>">
                    '<?= $outlet->name ?>'
                </a>
            </li>
            <li class="breadcrumb-item active">
                Tambah Diskon Baru
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3 class="mb-4">
        <i class="fa fa-plus"></i>
        Tambah Diskon Baru
    </h3>

    <div id="app">
        <discount-create
            :submit_url="<?= base_url("discount/store/{$outlet->id}") ?>"
            :redirect_url="<?= base_url("outletDiscount/detail/{$outlet->id}") ?>"
            :outlet='<?= json_encode($outlet) ?>'
            />
    </div>
</div>