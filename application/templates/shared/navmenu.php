<?php
    use App\Policies\OutletPolicy;
    use App\Policies\UserPolicy;
    use App\Policies\MenuCategoryPolicy;
    use App\Policies\OutletMenuPolicy;
    use App\Policies\DiscountPolicy;
    use App\Policies\ReceiptPrinterPolicy;
    use App\Policies\SalesInvoicePolicy;
    use App\Helpers\Auth;
    use App\Helpers\URL;
?>

<nav>
    <p class="nav-title"> MASTER DATA </p>
    <ul class="nav">
        <?php if (OutletPolicy::canIndex(Auth::user())) : ?>
            <li>
                <a href="<?= base_url("outlet/index") ?>">
                    <i class="fa-fw fa fa-building fa-2x"></i>
                    <span class=m-l:2> Outlet </span>
                </a>
            </li>
        <?php endif ?>

        <?php if (UserPolicy::canIndex(Auth::user())) : ?>
            <li>
                <a href="<?= base_url("user/index") ?>">
                    <i class="fa-fw fa fa-users fa-2x"></i>
                    <span class=m-l:2> Pengguna </span>
                </a>
            </li>
        <?php endif ?>

        <?php if (MenuCategoryPolicy::canIndex(Auth::user())) : ?>
            <li>
                <a href="<?= base_url("menuCategory/index") ?>">
                    <i class="fa-fw fa fa-list fa-2x"></i>
                    <span class=m-l:2> Kategori Menu & Menu </span>
                </a>
            </li>
        <?php endif ?>

        <?php if(OutletMenuPolicy::canIndex(Auth::user())): ?>

            <li>
                <a href="<?= base_url("outletMenu/index") ?>">
                    <i class="fa-fw fa fa-cutlery fa-2x"></i>
                    <span class=m-l:2> Menu Outlet </span>
                </a>
            </li>
            
        <?php endif ?>

        <?php if(DiscountPolicy::canIndex(Auth::user())): ?>

        <li>
            <a href="<?= base_url("outletDiscount/index") ?>">
                <i class="fa-fw fa fa-percent fa-2x"></i>
                <span class=m-l:2> Diskon </span>
            </a>
        </li>
        
        <?php endif ?>
    </ul>

    <p class="nav-title"> OPERASIONAL </p>

    <ul class="nav">
        <?php if (ReceiptPrinterPolicy::canIndex(Auth::user())) : ?>
            <li>
                <a href="<?= base_url("receiptPrinter/index") ?>">
                    <i class="fa-fw fa fa-print fa-2x"></i>
                    <span class=m-l:2> Printer </span>
                </a>
            </li>
        <?php endif ?>

        <?php if (SalesInvoicePolicy::canIndex(Auth::user())) : ?>
            <li>
                <a href="<?= base_url("salesInvoice/index") ?>">
                    <i class="fa-fw fa fa-usd fa-2x"></i>
                    <span class=m-l:2> Transaksi </span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>