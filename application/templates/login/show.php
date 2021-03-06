<?php ?>

<?php $this->layout("shared/base", ["title" => "Masuk"]) ?>

<div class="container">
    <div class="card width-sm m:a">
        <div class="card-header">
            Masuk <i class="fa fa-sign-in"></i>
        </div>
        <div class="card-block">
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
                        class="form-control <?= $this->has_error("username") ? "error" : "" ?>"
                        placeholder="Nama Pengguna"
                        value="<?= $this->old("username") ?>"
                        >
                    <?php if($this->has_error("username")): ?>
                    <label class='error'>
                        <?= $this->error("username") ?>
                    </label>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="password"> Kata Sandi: </label>
                    <input
                        name="password"
                        id="password"
                        type="password"
                        class="form-control <?= $this->has_error("password") ? "error" : "" ?>"
                        placeholder="Kata Sandi"
                        value=""
                        >
                    <?php if($this->has_error("password")): ?>
                    <label class='error'>
                        <?= $this->error("password") ?>
                    </label>
                    <?php endif ?>
                </div>
                
                <?php if($this->has_error("authentication")): ?>
                <div class="alert alert-danger">
                    <?= $this->error("authentication") ?>
                </div>
                <?php endif ?>
                
                <div class="t-a:r">
                    <button class="btn btn-primary">
                        Masuk
                        <i class="fa fa-sign-in"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>