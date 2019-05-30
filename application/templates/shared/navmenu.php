<?php
    use App\Policies\OutletPolicy;
    use App\Policies\UserPolicy;
    use App\Policies\MenuCategoryPolicy;
    use App\Policies\OutletMenuPolicy;
    use App\Policies\DiscountPolicy;
    use App\Helpers\Auth;
?>


<nav>
    <p class="nav-title"> MASTER DATA </p>
    <ul class="nav">
        <?php if (OutletPolicy::canIndex(Auth::user())) : ?>
            <li>
                <a href="<?= base_url("outlet/index") ?>">
                    <i class="fa-fw fa fa-building fa-2x"></i>
                    <span class="ml-2"> Outlet </span>
                </a>
            </li>
        <?php endif ?>

        <?php if (UserPolicy::canIndex(Auth::user())) : ?>
            <li>
                <a href="<?= base_url("user/index") ?>">
                    <i class="fa-fw fa fa-users fa-2x"></i>
                    <span class="ml-2"> Pengguna </span>
                </a>
            </li>
        <?php endif ?>

        <?php if (MenuCategoryPolicy::canIndex(Auth::user())) : ?>
            <li>
                <a href="<?= base_url("menuCategory/index") ?>">
                    <i class="fa-fw fa fa-list fa-2x"></i>
                    <span class="ml-2"> Kategori Menu & Menu </span>
                </a>
            </li>
        <?php endif ?>

        <?php if(OutletMenuPolicy::canIndex(Auth::user())): ?>

            <li>
                <a href="<?= base_url("outletMenu/index") ?>">
                    <i class="fa-fw fa fa-cutlery fa-2x"></i>
                    <span class="ml-2"> Menu Outlet </span>
                </a>
            </li>
            
        <?php endif ?>

        <?php if(DiscountPolicy::canIndex(Auth::user())): ?>

        <li>
            <a href="<?= base_url("outletDiscount/index") ?>">
                <i class="fa-fw fa fa-percent fa-2x"></i>
                <span class="ml-2"> Diskon </span>
            </a>
        </li>
        
        <?php endif ?>
    </ul>
</nav>