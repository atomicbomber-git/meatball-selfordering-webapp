<?php
    use App\Helpers\DefaultRoute;
use App\Helpers\Formatter;

?>

<?php $this->layout("shared/base", ["title" => "Sejarah Transaksi"]) ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="<?= base_url(DefaultRoute::get()) ?>"> Aplikasi Bakmi dan Bakso </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletFinishedSalesInvoice/index") ?>">
                    Histori Transaksi
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("outletFinishedSalesInvoice/detail/{$sales_invoice->outlet_id}") ?>">
                    '<?= $sales_invoice->outlet->name ?>'
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Invoice #<?= Formatter::salesInvoiceId($sales_invoice->id) ?>
            </li>
        </ol>
    </nav>

    <h3 class="mb-3">
        <i class="fa fa-book"></i>
        Invoice #<?= Formatter::salesInvoiceId($sales_invoice->id) ?>
    </h3>

    <?php $this->insert("shared/message") ?>

    <div class="card width-md">
        <div class="card-body">
            <dl>
                <dt> Tanggal / Waktu: </dt>
                <dd> <?= $sales_invoice->created_at ?> </dd>

                <dt> Outlet: </dt>
                <dd> <?= $sales_invoice->outlet->name ?> </dd>

                <dt> Waiter </dt>
                <dd> <?= $sales_invoice->waiter->name ?> </dd>

                <dt> Cashier </dt>
                <dd> <?= $sales_invoice->cashier->name ?> </dd>
            </dl>

            <h5> Daftar Item </h5>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="thead thead-dark">
                        <tr>
                            <th> Nama Item </th>
                            <th class="text-right"> Harga Satuan </th>
                            <th class="text-right"> Jumlah </th>
                            <th class="text-right"> Diskon </th>
                            <th class="text-right"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sales_invoice->sales_invoice_items as $sales_invoice_item): ?>
                        <tr>
                            <td> <?= $sales_invoice_item->name ?> </td>
                            <td class="text-right"> <?= Formatter::currency($sales_invoice_item->price) ?> </td>
                            <td class="text-right"> <?= $sales_invoice_item->quantity ?> </td>
                            <td class="text-right"> <?= Formatter::percent($sales_invoice_item->discount) ?> </td>
                            <td class="text-right">
                                <?= Formatter::currency($sales_invoice_item->final_price) ?>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot class="table-borderless">
                        <tr>
                            <th colspan="4" class="text-right"> Sub Total: </th>
                            <th class="text-right"> <?= Formatter::currency($sales_invoice->archived_total_price) ?> </th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right"> Service Charge (<?= Formatter::percent($sales_invoice->service_charge) ?>): </th>
                            <th class="text-right"> -<?= Formatter::currency($sales_invoice->archived_service_charge) ?> </th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right"> Pajak Pertambahan Nilai (<?= Formatter::percent($sales_invoice->pajak_pertambahan_nilai) ?>): </th>
                            <th class="text-right"> -<?= Formatter::currency($sales_invoice->archived_tax) ?> </th>
                        </tr>

                        <tr>
                            <th colspan="4" class="text-right"> Diskon Khusus (<?= Formatter::percent($sales_invoice->special_discount) ?>): </th>
                            <th class="text-right"> -<?= Formatter::currency($sales_invoice->archived_special_discount) ?> </th>
                        </tr>

                        <hr/>

                        <tr class="border-top">
                            <th colspan="4" class="text-right"> Rounding: </th>
                            <th class="text-right"> <?= Formatter::currency($sales_invoice->archived_rounding) ?> </th>
                        </tr>
                        
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->start("extra-scripts") ?>
    <?php $this->insert("shared/datatable") ?>
<?php $this->stop() ?>