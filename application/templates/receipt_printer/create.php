<?php
    use App\Helpers\DefaultRoute;
use App\EloquentModels\ReceiptPrinter;

?>

<?php $this->layout("shared/base", ["title" => "Ubah Printer"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <a href="<?= base_url("receiptPrinter/index") ?>">
                    Printer
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Tambahkan Printer Baru
            </li>
        </ol>
    </nav>


    <div class="card">
        <div class="card-body">

            <?php $this->insert("shared/message") ?>
            
            <form method="POST" action="<?= base_url("receiptPrinter/store") ?>" >
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
                  <label for="ipv4_address"> Alamat IP: </label>
                  <input
                      name="ipv4_address"
                      id="ipv4_address"
                      type="text"
                      class="form-control <?= $this->has_error("ipv4_address") ? "is-invalid" : "" ?>"
                      placeholder="Alamat IP"
                      value="<?= $this->old("ipv4_address") ?>"
                      >
                  <?php if($this->has_error("ipv4_address")): ?>
                  <div class="invalid-feedback">
                      <?= $this->error("ipv4_address") ?>
                  </div>
                  <?php endif ?>
              </div>

              <div class="form-group">
                  <label for="port"> Port: </label>
                  <input
                      name="port"
                      id="port"
                      type="text"
                      class="form-control <?= $this->has_error("port") ? "is-invalid" : "" ?>"
                      placeholder="Port"
                      value="<?= $this->old("port", 9100) ?>"
                      >
                  <?php if($this->has_error("port")): ?>
                  <div class="invalid-feedback">
                      <?= $this->error("port") ?>
                  </div>
                  <?php endif ?>
              </div>

              <div class="form-group">
                  <label for="type"> Tipe: </label>
                  <select
                      name="type"
                      id="type"
                      type="text"
                      class="form-control <?= $this->has_error("type") ? "is-invalid" : "" ?>"
                      placeholder="Tipe"
                      >
                    <?php foreach(ReceiptPrinter::TYPES as $code => $label): ?>
                    <option <?= $this->old("type") == $code ? 'selected' : '' ?> value="<?= $code ?>">
                        <?= $label ?>
                    </option>
                    <?php endforeach ?>

                  </select>
                  <?php if($this->has_error("type")): ?>
                  <div class="invalid-feedback">
                      <?= $this->error("type") ?>
                  </div>
                  <?php endif ?>
              </div>

              <div class="text-right">
                    <button class="btn btn-primary">
                        Tambahkan Printer Baru
                    </button>
              </div>
            </form>
        </div>
    </div>
</div>