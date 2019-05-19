<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
use App\Policies\UserPolicy;
use App\Helpers\Auth;

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
            <li class="breadcrumb-item active" aria-current="page">
                Pengguna
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3 class="mb-3">
        <i class="fa fa-users"></i>
        Pengguna
    </h3>

    <div class="d-flex justify-content-end my-3">
        <a href="<?= base_url("user/create") ?>" class="btn btn-dark">
            Tambahkan Pengguna Baru
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama Asli </th>
                        <th> Nama Pengguna </th>
                        <th> Hak Akses </th>
                        <th class="text-center"> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($users as $key => $user): ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $user->name ?> </td>
                        <td> <?= $user->username ?> </td>
                        <td> <?= $user->formatted_level ?> </td>
                        <td class="text-center">

                            <a class="btn btn-dark btn-sm" href="<?= base_url("user/edit/{$user->id}") ?>">
                                Ubah
                            </a>

                            <?php if(UserPolicy::canDelete(Auth::user(), $user)): ?>
                            <form class="d-inline-block" method="POST" action="<?= base_url("user/delete/{$user->id}") ?>" >
                                <input type="hidden"
                                    name="<?= $this->csrf_name() ?>"
                                    value="<?= $this->csrf_token() ?>">
                                <button class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </form>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>