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
                <a href="<?= base_url("menuItem/index/{$menu_item->menu_category_id}") ?>">
                    <?= $menu_item->menu_category->name ?>
                </a>
            </li>
            <li class="breadcrumb-item active">
                Ubah Menu
            </li>

        </ol>
    </nav>

    <h3 class="m-b:3">
        <i class="fa fa-list"></i>
        Ubah Menu Item '<?= $menu_item->name ?>'
    </h3>

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-block">
            <form method="POST" action="<?= base_url("menuItem/update/{$menu_item->id}") ?>" >
                <input type="hidden"
                    name="<?= $this->csrf_name() ?>"
                    value="<?= $this->csrf_token() ?>">
                
                <div class="form-group">
                    <label for="name"> Nama Menu: </label>
                    <input
                        name="name"
                        id="name"
                        type="text"
                        class="form-control <?= $this->has_error("name") ? "error" : "" ?>"
                        placeholder="Nama Menu"
                        value="<?= $this->old("name", $menu_item->name) ?>"
                        >
                    <?php if($this->has_error("name")): ?>
                    <label class='error'>
                        <?= $this->error("name") ?>
                    </label>
                    <?php endif ?>
                </div>

                <div class="t-a:r">
                    <button class="btn btn-primary">
                        Ubah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>