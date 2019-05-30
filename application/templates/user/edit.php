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

    <h3 class="m-b:3">
        <i class="fa fa-pencil"></i>
        Ubah Pengguna
    </h3>

    <div class="card">
        <div class="card-block">
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
                        class="form-control <?= $this->has_error("name") ? "error" : "" ?>"
                        placeholder="Nama Asli"
                        value="<?= $this->old("name", $user->name) ?>"
                        >
                    <?php if($this->has_error("name")): ?>
                    <div class='error'>
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
                        class="form-control <?= $this->has_error("username") ? "error" : "" ?>"
                        placeholder="Nama Pengguna"
                        value="<?= $this->old("username", $user->username) ?>"
                        >
                    <?php if($this->has_error("username")): ?>
                    <div class='error'>
                        <?= $this->error("username") ?>
                    </div>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="level"> Hak Akses: </label>
                    
                    <select name="level" class="form-control <?= $this->has_error("level") ? "error" : "" ?>" id="level">
                        <?php foreach(UserLevel::LEVELS as $level => $label): ?>
                        <option
                            <?= $this->old("password", $user->level) === $level ? 'selected' : '' ?>
                            value="<?= $level ?>">
                            <?= $label ?>
                        </option>
                        <?php endforeach ?>
                    </select>

                    <?php if($this->has_error("level")): ?>
                    <div class='error'>
                        <?= $this->error("level") ?>
                    </div>
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
                        value="<?= $this->old("password") ?>"
                        >
                        <small id="passwordHelpBlock" class="form-text text-muted">
                            Biarkan kolom ini kosong jika Anda tidak ingin mengubah kata sandi pengguna ini
                        </small>
                    <?php if($this->has_error("password")): ?>
                    <div class='error'>
                        <?= $this->error("password") ?>
                    </div>
                    <?php endif ?>
                </div>
            
                <div class="t-a:r">
                    <button class="btn btn-primary">
                        Ubah Pengguna
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>