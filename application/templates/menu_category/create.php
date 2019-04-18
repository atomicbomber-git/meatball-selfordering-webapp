<?php ?>

<?php $this->layout("shared/base", ["title" => "Tambahkan Kategori Menu Baru"]) ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            Tambahkan Kategori Menu Baru
            <i class="fa fa-plus"></i>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="<?= base_url("menuCategory/store") ?>" >
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
                        value="<?= $this->old("name") ?>"
                        >
                    <?php if($this->has_error("name")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("name") ?>
                    </div>
                    <?php endif ?>
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
                        Tambahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>