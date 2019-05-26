<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
?>

<?php $this->layout("shared/base", ["title" => "Tambahkan Kategori Menu Baru"]) ?>

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
                Tambahkan Kategori Menu
            </li>
        </ol>
    </nav>

    <h3 class="mb-3">
        <i class="fa fa-list"></i>
        Kategori Menu Baru
    </h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="<?= base_url("menuCategory/store") ?>">
                <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">

                <div class="form-group">
                    <label for="name"> Nama: </label>
                    <input name="name" id="name" type="text" class="form-control <?= $this->has_error("name") ? "is-invalid" : "" ?>" placeholder="Nama" value="<?= $this->old("name") ?>">
                    <?php if ($this->has_error("name")) : ?>
                        <div class="invalid-feedback">
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
                        class="form-control <?= $this->has_error("description") ? "is-invalid" : "" ?>"
                        placeholder="Deskripsi"
                        ><?= $this->old("description") ?></textarea>
                    <?php if($this->has_error("description")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("description") ?>
                    </div>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="image"> Gambar: </label>
                    <input name="image" id="image" type="file" <?= $this->has_error("image") ? "is-invalid" : "" ?>" placeholder="Gambar" value="<?= $this->old("image") ?>">
                    <?php if ($this->has_error("image")) : ?>
                        <div class="invalid-feedback">
                            <?= $this->error("image") ?>
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