<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
?>

<?php $this->layout("shared/base", ["title" => "Ubah Kategori Menu"]) ?>

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
        <li class="breadcrumb-item active" aria-current="page">
            Ubah Kategori Menu
        </li>
    </ol>
</nav>

<?php $this->insert("shared/message") ?>

<div class="card">
    <div class="card-block">
        <form method="POST" enctype="multipart/form-data" action="<?= base_url("menuCategory/update/{$menu_category->id}") ?>">
            <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">

            <div class="form-group">
                <label for="name"> Nama: </label>
                <input name="name" id="name" type="text" class="form-control <?= $this->has_error("name") ? "error" : "" ?>" placeholder="Nama" value="<?= $this->old("name", $menu_category->name) ?>">
                <?php if ($this->has_error("name")) : ?>
                    <div class='error'>
                        <?= $this->error("name") ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="form-group">
                <label for="priority"> Nilai Prioritas Urutan: </label>
                <input
                    name="priority"
                    id="priority"
                    type="text"
                    class="form-control <?= $this->has_error("priority") ? "error" : "" ?>"
                    placeholder="Nilai Prioritas Urutan"
                    value="<?= $this->old("priority", $menu_category->priority) ?>"
                    >
                <?php if($this->has_error("priority")): ?>
                <div class="error">
                    <?= $this->error("priority") ?>
                </div>
                <?php endif ?>
            </div>

            <div class="form-group">
                <label for="column"> Kolom Tempat Kategori Menu Berada: </label>
                <input
                    name="column"
                    id="column"
                    type="text"
                    class="form-control <?= $this->has_error("column") ? "error" : "" ?>"
                    placeholder="Kolom Tempat Kategori Menu Berada"
                    value="<?= $this->old("column", $menu_category->column) ?>"
                    >
                <?php if($this->has_error("column")): ?>
                <div class="error">
                    <?= $this->error("column") ?>
                </div>
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