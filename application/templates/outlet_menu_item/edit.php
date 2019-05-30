<?php
use App\Helpers\Formatter;
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
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletMenu/detail/{$outlet_menu_item->outlet->id}") ?>">
                    '<?= $outlet_menu_item->outlet->name ?>'
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletMenuItem/index/{$outlet_menu_item->outlet->id}/{$outlet_menu_item->menu_item->menu_category->id}") ?>">
                    '<?= $outlet_menu_item->menu_item->menu_category->name ?>'
                </a>
            </li>
            <li class="breadcrumb-item">
                Ubah Menu
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3>
        <i class="fa fa-cutlery"></i>
        Ubah Menu '<?= $outlet_menu_item->menu_item->name ?>' <br />
    </h3>

    <div class="card">
        <div class="card-block">

            <form method="POST" action="<?= base_url("outletMenuItem/update/{$outlet_menu_item->id}") ?>">
                <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
                <div class="form-group">
                    <label for="price"> Harga (Rp.): </label>
                    <input name="price" id="price" type="text" class="form-control <?= $this->has_error("price") ? "error" : "" ?>" placeholder="Harga" value="<?= $this->old("price", Formatter::number($outlet_menu_item->price)) ?>">
                    <?php if ($this->has_error("price")) : ?>
                        <div class='error'>
                            <?= $this->error("price") ?>
                        </div>
                    <?php endif ?>
                </div>

                <div class="t-a:r">
                    <button class="btn btn-primary">
                        Ubah Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->start("extra-scripts") ?>
<script>
    var cleave = new Cleave('#price', {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.'
    })

    $("form").submit(function() {
        $("#price").val(cleave.getRawValue())
    })

    // console.log(cleave.getRawValue())

</script>
<?php $this->stop() ?>