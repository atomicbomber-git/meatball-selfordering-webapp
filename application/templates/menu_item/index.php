<?php ?>

<?php $this->layout("shared/base", ["title" => "Menu"]) ?>

<div class="container">
    <div class="text-right my-3">
        <a
            href="<?= base_url('menuCategory/create') ?>"
            class="btn btn-dark">
            Tambahkan Menu Baru
        </a>
    </div>

    <?php $this->insert("shared/message") ?>

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
                    <?php foreach($menu_category->menu_items as $key => $menu_item): ?>
                    <tr>
                        <td> <?= $key + 1 ?> </td>
                        <td> <?= $menu_item->name ?> </td>
                        <td>
                            
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>