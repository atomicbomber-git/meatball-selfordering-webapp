<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Enums\UserLevel;
?>

<?php $this->layout("shared/base", ["title" => "Tambah Pengguna Baru"]) ?>

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
            Tambahkan Pengguna Baru
        </li>
    </ol>
</nav>

<?php $this->insert("shared/message") ?>

<h3 class="m-b:3">
    <i class="fa fa-plus"></i>
    Tambahkan Pengguna Baru
</h3>

<div class="card">
    <div class="card-block">
        <form method="POST" action="<?= base_url("user/store") ?>">
            <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">

            <div class="form-group">
                <label for="name"> Nama Asli: </label>
                <input name="name" id="name" type="text" class="form-control <?= $this->has_error("name") ? "error" : "" ?>" placeholder="Nama Asli" value="<?= $this->old("name") ?>">
                <?php if ($this->has_error("name")) : ?>
                    <label class='error'>
                        <?= $this->error("name") ?>
                    </label>
                <?php endif ?>
            </div>

            <div class="form-group">
                <label for="username"> Nama Pengguna: </label>
                <input name="username" id="username" type="text" class="form-control <?= $this->has_error("username") ? "error" : "" ?>" placeholder="Nama Pengguna" value="<?= $this->old("username") ?>">
                <?php if ($this->has_error("username")) : ?>
                    <label class='error'>
                        <?= $this->error("username") ?>
                    </label>
                <?php endif ?>
            </div>

            <div class="form-group">
                <label for="level"> Hak Akses: </label>

                <select name="level" class="form-control <?= $this->has_error("level") ? "error" : "" ?>" id="level">
                    <?php foreach (UserLevel::LEVELS as $level => $label) : ?>
                        <option value="<?= $level ?>">
                            <?= $label ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <?php if ($this->has_error("level")) : ?>
                    <label class='error'>
                        <?= $this->error("level") ?>
                    </label>
                <?php endif ?>
            </div>

            <div class="form-group">
                <label for="password"> Kata Sandi: </label>
                <input name="password" id="password" type="password" class="form-control <?= $this->has_error("password") ? "error" : "" ?>" placeholder="Kata Sandi" value="<?= $this->old("password") ?>">
                <?php if ($this->has_error("password")) : ?>
                    <label class='error'>
                        <?= $this->error("password") ?>
                    </label>
                <?php endif ?>
            </div>

            <div class="t-a:r">
                <button class="btn btn-primary">
                    Tambahkan Pengguna Baru
                </button>
            </div>

        </form>
    </div>
</div