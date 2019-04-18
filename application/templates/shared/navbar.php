<?php 
    use App\Helpers\Auth;
use App\Policies\ItemTypePolicy;

?>

<nav class="navbar navbar-dark bg-dark navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url("/") ?>">
            Admin Bakso
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <?php if(Auth::check()): ?>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav mr-auto">

                <?php if(Auth::check()): ?>

                <?php if(ItemTypePolicy::canIndex(Auth::user())): ?>
                
                <li class='nav-item active'>
                    <a class='nav-link' href="<?= base_url("itemType/index") ?>" >
                        <i class='fa fa-cutlery'></i>
                        Kategori Item
                    </a>
                </li>

                <?php endif ?>
                <?php endif ?>
            </div>

            <?php if(Auth::check()): ?>

            <form method="POST" action="<?= base_url("logout") ?>" >
                <input type="hidden"
                    name="<?= $this->csrf_name() ?>"
                    value="<?= $this->csrf_token() ?>">
                
                <button class="btn btn-danger">
                    Log Out
                    <i class="fa fa-sign-out"></i>
                </button>
            </form>
            <?php endif ?>
        </div>

        <?php endif ?>
    </div>
</nav>

