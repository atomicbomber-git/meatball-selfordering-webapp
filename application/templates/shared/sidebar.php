<?php 
    use App\Helpers\Auth;
?>

<div class="brand">
    <!-- toggle offscreen menu -->
    <a href="javascript:;" data-toggle="sidebar" class="toggle-offscreen hidden-lg-up">
        <i class="material-icons">menu</i>
    </a>
    <!-- /toggle offscreen menu -->
    <!-- logo -->
    <a class="brand-logo">
        <img class="expanding-hidden" src="<?= base_url('assets/images/logo.png') ?>" alt="" />
    </a>
    <!-- /logo -->
</div>
<div class="nav-profile dropdown">
    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
        <div class="user-image">
            <img src="<?= base_url('assets/images/avatar.jpg') ?>" class="avatar img-circle" alt="user" title="user" />
        </div>
        <div class="user-info expanding-hidden">
            <?= Auth::user()->name ?? 'Guest' ?>
        </div>
    </a>
    <div class="dropdown-menu">
        <a id="logout_button" class="dropdown-item">
            <i class="fa fa-sign-out"></i>
            Logout
        </a>

        <form id="logout_form" method="POST" action="<?= base_url("logout/handle") ?>">
            <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
        </form>
    </div>
</div>
<!-- main navigation -->

<?php $this->insert("shared/navmenu") ?>