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
                        <th> Gambar </th>
                        <th> Kendali </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($item_types as $key => $item_type): ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $item_type->name ?> </td>
                        <td>
                            <img
                                style="width: 320px; height: 240px; object-fit: cover"
                                src="<?= base_url("itemType/image/{$item_type->id}") ?>"
                                alt="<?= $item_type->name ?>">
                        </td>
                        <td>
                            <form class="d-inline-block" method="POST" action="<?= base_url("itemType/delete/{$item_type->id}") ?>" >
                                <input type="hidden"
                                    name="<?= $this->csrf_name() ?>"
                                    value="<?= $this->csrf_token() ?>">
                                
                                <button class="btn btn-sm btn-danger">
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
</div>