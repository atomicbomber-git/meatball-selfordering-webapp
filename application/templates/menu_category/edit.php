<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
?>

<?php $this->layout("shared/base", ["title" => "Ubah Kategori Menu"]) ?>

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
                    <label for="description"> Deskripsi: </label>
                    <textarea
                        name="description"
                        id="description"
                        type="text"
                        rows="6"
                        class="form-control <?= $this->has_error("description") ? "error" : "" ?>"
                        placeholder="Deskripsi"
                        ><?= $this->old("description", $menu_category->description) ?></textarea>
                    <?php if($this->has_error("description")): ?>
                    <label class='error'>
                        <?= $this->error("description") ?>
                    </label>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="current_image"> Gambar Lama: </label>
                    <img style="width: 640px; height: 480px; display: block; object-fit: cover" src="<?= base_url("menuCategory/image/{$menu_category->id}") ?>" alt="<?= $menu_category->name ?>">
                </div>

                <div class="form-group">
                    <label for="image"> Gambar Baru: </label>
                    <input name="image" id="image" type="file" class="form-control <?= $this->has_error("image") ? "error" : "" ?>" placeholder="Gambar" value="<?= $this->old("image") ?>">
                    <?php if ($this->has_error("image")) : ?>
                        <div class='error'>
                            <?= $this->error("image") ?>
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
</div>