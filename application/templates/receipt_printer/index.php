<?php ?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

<div class="container">
    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama </th>
                        <th> Tipe </th>
                        <th> IP Address </th>
                        <th> Port </th>
                        <th> Kendali </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($receipt_printers as $key => $receipt_printer): ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $receipt_printer->name ?> </td>
                        <td> <?= $receipt_printer->type ?> </td>
                        <td> <?= $receipt_printer->ipv4_address ?> </td>
                        <td> <?= $receipt_printer->port ?> </td>
                        <td>
                            <form method="POST" action="<?= base_url("receiptPrinter/test/{$receipt_printer->id}") ?>" >
                                <input type="hidden"
                                    name="<?= $this->csrf_name() ?>"
                                    value="<?= $this->csrf_token() ?>">

                                <button class="btn btn-dark btn-sm">
                                    Test
                                    <i class="fa fa-print"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>