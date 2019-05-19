<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Enums\UserLevel;
?>

<?php $this->layout("shared/base", ["title" => "Pengguna"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>">
                    <?= AppInfo::name() ?>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("user/index") ?>">
                    Pengguna
                </a>
            </li>
            <li class="breadcrumb-item">
                Ubah Pengguna
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3 class="mb-3">
        <i class="fa fa-pencil"></i>
        Ubah Pengguna
    </h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url("user/update/{$user->id}") ?>" >
                <input type="hidden"
                    name="<?= $this->csrf_name() ?>"
                    value="<?= $this->csrf_token() ?>">

                <div class="form-group">
                    <label for="name"> Nama Asli: </label>
                    <input
                        name="name"
                        id="name"
                        type="text"
                        class="form-control <?= $this->has_error("name") ? "is-invalid" : "" ?>"
                        placeholder="Nama Asli"
                        value="<?= $this->old("name", $user->name) ?>"
                        >
                    <?php if($this->has_error("name")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("name") ?>
                    </div>
                    <?php endif ?>
                </div>
                
                <div class="form-group">
                    <label for="username"> Nama Pengguna: </label>
                    <input
                        name="username"
                        id="username"
                        type="text"
                        class="form-control <?= $this->has_error("username") ? "is-invalid" : "" ?>"
                        placeholder="Nama Pengguna"
                        value="<?= $this->old("username", $user->username) ?>"
                        >
                    <?php if($this->has_error("username")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("username") ?>
                    </div>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="level"> Hak Akses: </label>
                    
                    <select name="level" class="form-control <?= $this->has_error("level") ? "is-invalid" : "" ?>" id="level">
                        <?php foreach(UserLevel::LEVELS as $level => $label): ?>
                        <option
                            <?= $this->old("password", $user->level) === $level ? 'selected' : '' ?>
                            value="<?= $level ?>">
                            <?= $label ?>
                        </option>
                        <?php endforeach ?>
                    </select>

                    <?php if($this->has_error("level")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("level") ?>
                    </div>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="password"> Kata Sandi: </label>
                    <input
                        name="password"
                        id="password"
                        type="text"
                        class="form-control <?= $this->has_error("password") ? "is-invalid" : "" ?>"
                        placeholder="Kata Sandi"
                        value="<?= $this->old("password") ?>"
                        >
                        <small id="passwordHelpBlock" class="form-text text-muted">
                            Biarkan kolom ini kosong jika Anda tidak ingin mengubah kata sandi pengguna ini
                        </small>
                    <?php if($this->has_error("password")): ?>
                    <div class="invalid-feedback">
                        <?= $this->error("password") ?>
                    </div>
                    <?php endif ?>
                </div>
            
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">
                        Ubah Pengguna
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>