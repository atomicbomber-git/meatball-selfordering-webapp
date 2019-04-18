<?php ?>

<?php $this->layout("shared/base", ["title" => "Tambahkan Tipe Item Baru"]) ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            Tambahkan Tipe Item Baru
            <i class="fa fa-plus"></i>
        </div>

        <div class="card-body">
            <form method="POST" action="<?= base_url("itemType/store") ?>" >
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
            </form>
        </div>
    </div>
</div>