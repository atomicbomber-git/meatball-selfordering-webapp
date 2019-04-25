<?php ?>

<?php $this->layout("shared/base", ["title" => "Ubah Kategori Menu"]) ?>

<div class="container">

    <?php $this->insert("shared/message") ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="<?= base_url("menuCategory/update/{$menu_category->id}") ?>" >
                <input type="hidden"
                    name="<?= $this->csrf_name() ?>"
                    value="<?= $this->csrf_token() ?>">
                
                <div class="form-group">
                    <label for="name"> Nama: </label>
                    <input
                        name="name"
                        id="name"
                        type="text"
                        class="form-control <?= $this->has_error("name") ? "is-invalid" : "" ?>"
                        placeholder="Nama"
                        value="<?= $this->old("name", $menu_category->name) ?>"
                        >
                    <?php if($this->has_error("name")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("name") ?>
                    </div>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="current_image"> Gambar Lama: </label>
                    <img
                        style="width: 640px; height: 480px; display: block; object-fit: cover"
                        src="<?= base_url("menuCategory/image/{$menu_category->id}") ?>"
                        alt="<?= $menu_category->name ?>">
                </div>

                <div class="form-group">
                    <label for="image"> Gambar: </label>
                    <input
                        name="image"
                        id="image"
                        type="file"
                        class="form-control <?= $this->has_error("image") ? "is-invalid" : "" ?>"
                        placeholder="Gambar"
                        value="<?= $this->old("image") ?>"
                        >
                    <?php if($this->has_error("image")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("image") ?>
                    </div>
                    <?php endif ?>
                </div>

                <div class="text-right">
                    <button class="btn btn-primary">
                        Ubah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>