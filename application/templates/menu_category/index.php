<?php ?>

<?php $this->layout("shared/base", ["title" => "Kategori Menu"]) ?>

<div class="container">
    <div class="text-right my-3">
        <a
            href="<?= base_url('menuCategory/create') ?>"
            class="btn btn-dark">
            Tambahkan Kategori Menu Baru
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
                    <?php foreach($menu_categories as $key => $menu_category): ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $menu_category->name ?> </td>
                        <td>
                            <img
                                style="width: 320px; height: 240px; object-fit: cover"
                                src="<?= base_url("menuCategory/image/{$menu_category->id}") ?>"
                                alt="<?= $menu_category->name ?>">
                        </td>
                        <td>
                            <a href="<?= base_url("menuCategory/edit/{$menu_category->id}") ?>" class="btn btn-sm btn-dark">
                                Ubah
                            </a>

                            <form class="d-inline-block" method="POST" action="<?= base_url("menuCategory/delete/{$menu_category->id}") ?>" >
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