<?php
use App\Helpers\Formatter;

$this->layout("shared/base", ["title" => "Konfirmasi Transaksi"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <a href="<?= base_url("salesInvoice/index") ?>">
                    Transaksi
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <a href="<?= base_url("salesInvoice/confirm/{$sales_invoice->id}") ?>">
                    Konfirmasi Transaksi
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Revisi Transaksi
            </li>
        </ol>
    </nav>

    <?php $this->insert("shared/message") ?>

    <h1 class="mb-1"> Konfirmasi Transaksi </h1>
    <hr class="mt-0"/>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-striped table-bordered">
                <thead class="thead thead-dark">
                    <tr>
                        <th> Item </th>
                        <th class="text-right"> Jumlah </th>
                        <th class="text-right"> Harga </th>
                        <th class="text-right"> Subtotal </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($sales_invoice->planned_sales_invoice_items as $sales_invoice_item): ?>
                    <tr>
                        <td> <?= $sales_invoice_item->menu_item->name ?> </td>
                        <td class="text-right"> <?= $sales_invoice_item->quantity ?> </td>
                        <td class="text-right">
                            Rp.
                            <?= Formatter::currency($sales_invoice_item->menu_item->outlet_menu_item->price) ?>
                        </td>
                        <td class="text-right">
                            Rp.
                            <?=
                                Formatter::currency(
                                    $sales_invoice_item->quantity *
                                    $sales_invoice_item->menu_item->outlet_menu_item->price
                                )
                            ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <h3 class="text-right"> Total:
                <span class="text-danger">
                    Rp.
                    <?=
                        Formatter::currency(
                            $sales_invoice->planned_sales_invoice_items->sum(function ($sales_invoice_item) {
                                return $sales_invoice_item->quantity *
                                    $sales_invoice_item->menu_item->outlet_menu_item->price;
                            })
                        )
                    ?>
                </span>
            </h3>

            <div class="text-right mt-5">
                <a href="<?= base_url("salesInvoice/updateAndConfirm/{$sales_invoice->id}") ?>" class="btn btn-dark">
                    Revisi Transaksi
                </a>

                <form
                    id="confirm-sales-invoice"
                    class="d-inline-block"
                    method="POST" action="<?= base_url("salesInvoice/processConfirm/{$sales_invoice->id}") ?>" >
                    <input type="hidden"
                        name="<?= $this->csrf_name() ?>"
                        value="<?= $this->csrf_token() ?>">
                    
                    <button class="btn btn-primary">
                        Konfirmasi Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->start('extra-scripts') ?>

<script>
    $(document).ready(function() {
        $("form#confirm-sales-invoice").submit(function (e) {
            e.preventDefault()

            swal({
                icon: "warning",
                text: "Apakah Anda yakin Anda ingin mengkonfirmasi pesanan ini?s",
                buttons: ["Tidak", "Ya"],
            })
            .then(is_ok => {
                if (is_ok) {
                    $(this).off("submit").submit()
                }
            })

        });
    })
</script>

<?php $this->stop() ?>