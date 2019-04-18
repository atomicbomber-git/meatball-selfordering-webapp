<?php ?>

<?php $this->layout("shared/base", ["title" => "Masuk"]) ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            Masuk <i class="fa fa-sign-in"></i>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= base_url("login/handle") ?>" >
                <input type="hidden"
                    name="<?= $this->csrf_name() ?>"
                    value="<?= $this->csrf_token() ?>">
                
                <div class="form-group">
                    <label for="username"> Nama Pengguna: </label>
                    <input
                        name="username"
                        id="username"
                        type="text"
                        class="form-control <?= $this->has_error("username") ? "is-invalid" : "" ?>"
                        placeholder="Nama Pengguna"
                        value="<?= $this->old("username") ?>"
                        >
                    <?php if($this->has_error("username")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("username") ?>
                    </div>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="password"> Kata Sandi: </label>
                    <input
                        name="password"
                        id="password"
                        type="password"
                        class="form-control <?= $this->has_error("password") ? "is-invalid" : "" ?>"
                        placeholder="Kata Sandi"
                        value="<?= $this->old("password") ?>"
                        >
                    <?php if($this->has_error("password")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("password") ?>
                    </div>
                    <?php endif ?>
                </div>
                
                <?php if($this->has_error("authentication")): ?>
                <div class="alert alert-danger">
                    <?= $this->error("authentication") ?>
                </div>
                <?php endif ?>
                
                <div class="text-right">
                    <button class="btn btn-primary">
                        Masuk
                        <i class="fa fa-sign-in"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>