<?php
    use App\Helpers\DefaultRoute;
    use App\Helpers\AppInfo;
?>

<?php $this->layout("shared/base", ["title" => "Menu"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>">
                    <?= AppInfo::name() ?>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("menuCategory/index") ?>">
                    Kategori Menu
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("menuItem/index/{$menu_category->id}") ?>">
                    <?= $menu_category->name ?>
                </a>
            </li>
            <li class="breadcrumb-item active">
                Tambahkan Menu Baru
            </li>

        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3>
        Detail Kategori Menu '<?= $menu_category->name ?>'
    </h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url("menuItem/store/{$menu_category->id}") ?>" >
                <input type="hidden"
                    name="<?= $this->csrf_name() ?>"
                    value="<?= $this->csrf_token() ?>">
                
                <div class="form-group">
                    <label for="name"> Nama Menu: </label>
                    <input
                        name="name"
                        id="name"
                        type="text"
                        class="form-control <?= $this->has_error("name") ? "is-invalid" : "" ?>"
                        placeholder="Nama Menu"
                        value="<?= $this->old("name") ?>"
                        >
                    <?php if($this->has_error("name")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("name") ?>
                    </div>
                    <?php endif ?>
                </div>

                <div class="text-right">
                    <button class="btn btn-primary">
                        Tambahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>