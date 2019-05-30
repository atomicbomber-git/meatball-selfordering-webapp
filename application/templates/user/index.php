<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Policies\UserPolicy;
use App\Helpers\Auth;
?>

<?php $this->layout("shared/base", ["title" => "Pengguna"]) ?>


<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">
            <a href="<?= base_url(DefaultRoute::get()) ?>">
                <?= AppInfo::name() ?>
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            Pengguna
        </li>
    </ol>
</nav>

<?php $this->insert("shared/message") ?>

<h3 class="m-b:3">
    <i class="fa fa-users"></i>
    Pengguna
</h3>

<div class="t-a:r m-y:3">
    <a href="<?= base_url("user/create") ?>" class="btn btn-info">
        Tambahkan Pengguna Baru
    </a>
</div>

<div class="card">
    <div class="card-block">
        <table class="datatable table table-sm table-bordered table-striped">
            <thead class="thead thead-dark">
                <tr>
                    <th> # </th>
                    <th> Nama Asli </th>
                    <th> Nama Pengguna </th>
                    <th> Hak Akses </th>
                    <th class="t-a:c"> Aktif / Non-Aktif </th>
                    <th class="t-a:c"> Kendali </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($users as $key => $user) : ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $user->name ?> </td>
                        <td> <?= $user->username ?> </td>
                        <td> <?= $user->formatted_level ?> </td>
                        <td class="t-a:c">
                            <?php $this->insert("shared/activation_status", [
                                "is_active" => $user->is_active
                            ]) ?>
                        </td>
                        <td class="t-a:c">

                            <a class="btn btn-info btn-sm" href="<?= base_url("user/edit/{$user->id}") ?>">
                                Ubah
                            </a>

                            <?php if ($user->is_active) : ?>

                                <form class="d-inline-block confirmed" method="POST" action="<?= base_url("user/deactivate/{$user->id}") ?>">
                                    <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
                                    <button <?= UserPolicy::canToggleActivationStatus(Auth::user(), $user) ? '' : 'disabled' ?> class="btn btn-warning btn-sm">
                                        Non-Aktifkan
                                    </button>
                                </form>

                            <?php else : ?>

                                <form class="d-inline-block confirmed" method="POST" action="<?= base_url("user/activate/{$user->id}") ?>">
                                    <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
                                    <button <?= UserPolicy::canToggleActivationStatus(Auth::user(), $user) ? '' : 'disabled' ?> class="btn btn-success btn-sm">
                                        Aktifkan
                                    </button>
                                </form>

                            <?php endif ?>

                            <form class="d-inline-block confirmed" method="POST" action="<?= base_url("user/delete/{$user->id}") ?>">
                                <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">

                                <button <?= UserPolicy::canDelete(Auth::user(), $user) ? '' : 'disabled' ?> class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>


<?php $this->start("extra-scripts") ?>
<?php $this->insert("shared/datatable") ?>
<?php $this->stop() ?>