<?php ?>

<?php $this->layout('shared/base', ["title" => "Welcome"]) ?>

<div class="container">
    <div class="card">
        <div class="card-body">

            <form method="POST" action="<?= base_url("logout") ?>" >
                <input type="hidden"
                    name="<?= $this->csrf_name() ?>"
                    value="<?= $this->csrf_token() ?>">
                
                <button class="btn btn-sm btn-danger">
                    Log Out
                    <i class="fa fa-trash"></i>
                </button>
            </form>

            <p>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit. Magni, tempora ipsa doloribus incidunt magnam dolor! Ducimus, minima nulla, debitis provident sed iure dignissimos ea, quo magnam quis harum laboriosam similique.
            </p>

            <div id="app">
                <home> </home>
            </div>
        </div>
    </div>
</div>