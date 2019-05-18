<?php
use App\Helpers\DefaultRoute;
use App\Helpers\AppInfo;
?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>">
                    <?= AppInfo::name() ?>
                </a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <a href="<?= base_url("outlet/index") ?>">
                    Outlet
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Ubah Outlet '<?= $outlet->name ?>'
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h3 class="mb-3">
        <i class="fa fa-building"></i>
        Ubah Outlet '<?= $outlet->name ?>'
    </h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= base_url("outlet/update/{$outlet->id}") ?>" >
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
                            value="<?= $this->old("name", $outlet->name) ?>"
                            >
                        <?php if($this->has_error("name")): ?>
                        <div class="invalid-feedback">
                            <?= $this->error("name") ?>
                        </div>
                        <?php endif ?>
                    </div>

                    <div class="form-group">
                        <label for="address"> Alamat: </label>
                        <input
                            name="address"
                            id="address"
                            type="text"
                            class="form-control <?= $this->has_error("address") ? "is-invalid" : "" ?>"
                            placeholder="Alamat"
                            value="<?= $this->old("address", $outlet->address) ?>"
                            >
                        <?php if($this->has_error("address")): ?>
                        <div class="invalid-feedback">
                            <?= $this->error("address") ?>
                        </div>
                        <?php endif ?>
                    </div>

                    <div class="form-group">
                        <label for="phone"> Nomor Telefon: </label>
                        <input
                            name="phone"
                            id="phone"
                            type="text"
                            class="form-control <?= $this->has_error("phone") ? "is-invalid" : "" ?>"
                            placeholder="Nomor Telefon"
                            value="<?= $this->old("phone", $outlet->phone) ?>"
                            >
                        <?php if($this->has_error("phone")): ?>
                        <div class="invalid-feedback">
                            <?= $this->error("phone") ?>
                        </div>
                        <?php endif ?>
                    </div>

                    <div class="form-group">
                        <label for="pajak_pertambahan_nilai"> Pajak Pertambahan Nilai (%): </label>
                        <input
                            name="pajak_pertambahan_nilai"
                            id="pajak_pertambahan_nilai"
                            type="number"
                            min="0"
                            max="100" step="0.01"
                            class="form-control <?= $this->has_error("pajak_pertambahan_nilai") ? "is-invalid" : "" ?>"
                            placeholder="Pajak Pertambahan Nilai"
                            value="<?= $this->old("pajak_pertambahan_nilai", $outlet->pajak_pertambahan_nilai * 100) ?>"
                            >
                        <?php if($this->has_error("pajak_pertambahan_nilai")): ?>
                        <div class="invalid-feedback">
                            <?= $this->error("pajak_pertambahan_nilai") ?>
                        </div>
                        <?php endif ?>
                    </div>

                    <div class="form-group">
                        <label for="service_charge"> Service Charge (%): </label>
                        <input
                            name="service_charge"
                            id="service_charge"
                            type="number"
                            min="0" max="100" step="0.01"
                            class="form-control <?= $this->has_error("service_charge") ? "is-invalid" : "" ?>"
                            placeholder="Service Charge"
                            value="<?= $this->old("service_charge", $outlet->service_charge * 100) ?>"
                            >
                        <?php if($this->has_error("service_charge")): ?>
                        <div class="invalid-feedback">
                            <?= $this->error("service_charge") ?>
                        </div>
                        <?php endif ?>
                    </div>

                    <div class="form-group">
                        <label for="npwpd"> NPWPD: </label>
                        <input
                            name="npwpd"
                            id="npwpd"
                            type="text"
                            class="form-control <?= $this->has_error("npwpd") ? "is-invalid" : "" ?>"
                            placeholder="NPWPD"
                            value="<?= $this->old("npwpd", $outlet->npwpd) ?>"
                            >
                        <?php if($this->has_error("npwpd")): ?>
                        <div class="invalid-feedback">
                            <?= $this->error("npwpd") ?>
                        </div>
                        <?php endif ?>
                    </div>

                    <div class="form-group">
                        <label for="print_server_url"> URL Print Server: </label>
                        <input
                            name="print_server_url"
                            id="print_server_url"
                            type="text"
                            class="form-control <?= $this->has_error("print_server_url") ? "is-invalid" : "" ?>"
                            placeholder="URL Print Server"
                            value="<?= $this->old("print_server_url", $outlet->print_server_url) ?>"
                            >
                        <?php if($this->has_error("print_server_url")): ?>
                        <div class="invalid-feedback">
                            <?= $this->error("print_server_url") ?>
                        </div>
                        <?php endif ?>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">
                            Ubah Data
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>