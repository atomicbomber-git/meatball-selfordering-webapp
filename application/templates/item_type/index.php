<?php ?>

<?php $this->layout("shared/base", ["title" => "Tipe Item"]) ?>

<div class="container">
    <div class="text-right my-3">
        <a
            href="<?= base_url('itemType/create') ?>"
            class="btn btn-dark">
            Tambahkan Tipe Item Baru
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead thead-dark">
                    <tr>
                        <th> # </th>
                        <th> Nama </th>
                        <th> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($item_types as $key => $item_type): ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $item_type->name ?> </td>
                        <td>
                            <button class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>