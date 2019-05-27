<?php
use App\Helpers\Auth;
use App\Policies\MenuCategoryPolicy;
use App\Helpers\DefaultRoute;
use App\Policies\SalesInvoicePolicy;
use App\Policies\ReceiptPrinterPolicy;
use App\Helpers\URL;
use App\Policies\OutletPolicy;
use App\Policies\UserPolicy;
use App\Policies\OutletMenuPolicy;
use App\Policies\DiscountPolicy;

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url(DefaultRoute::get()) ?>">
            Admin Bakso
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <?php if (Auth::check()) : ?>

            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav mr-auto">

                    <?php if (Auth::check()) : ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Master Data
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                                <?php if(OutletPolicy::canIndex(Auth::user())): ?>
                                <a href="<?= base_url("outlet/index") ?>" class="dropdown-item">
                                    <i class="fa fa-building"></i>
                                    Outlet
                                </a>
                                <?php endif ?>
                                
                                <?php if(UserPolicy::canIndex(Auth::user())): ?>
                                <a href="<?= base_url("user/index") ?>" class="dropdown-item">
                                    <i class="fa fa-users"></i>
                                    Pengguna
                                </a>
                                <?php endif ?>

                                <?php if (MenuCategoryPolicy::canIndex(Auth::user())) : ?>

                                <a class="dropdown-item" href="<?= base_url("menuCategory/index") ?>">
                                    <i class='fa fa-list'></i>
                                    Kategori Menu & Menu
                                </a>

                                <?php endif ?>

                                <?php if(OutletMenuPolicy::canIndex(Auth::user())): ?>

                                <a href="<?= base_url("outletMenu/index") ?>" class="dropdown-item">
                                    <i class="fa fa-cutlery"></i>
                                    Menu Outlet
                                </a>
                                    
                                <?php endif ?>

                                <?php if(DiscountPolicy::canIndex(Auth::user())): ?>
                                
                                <a href="<?= base_url("outletDiscount/index") ?>" class="dropdown-item">
                                    <i class="fa fa-percent"></i>
                                    Diskon
                                </a>

                                <?php endif ?>
                            </div>
                        </li>

                        <?php if (ReceiptPrinterPolicy::canIndex(Auth::user())) : ?>

                            <li class='nav-item <?= URL::has("receiptPrinter") ? "active" : "" ?>'>
                                <a class='nav-link' href="<?= base_url("receiptPrinter/index") ?>">
                                    <i class='fa fa-print'></i>
                                    Printer
                                </a>
                            </li>

                        <?php endif ?>

                        <?php if (SalesInvoicePolicy::canIndex(Auth::user())) : ?>

                            <li class='nav-item <?= URL::has("salesInvoice") ? "active" : "" ?>'>
                                <a class='nav-link' href="<?= base_url("salesInvoice/index") ?>">
                                    <i class='fa fa-usd'></i>
                                    Transaksi
                                </a>
                            </li>

                        <?php endif ?>

                        <?php if (TRUE) : ?>
                            <li class='nav-item <?= URL::has("outletFinishedSalesInvoice") ? "active" : "" ?>'>
                                <a class='nav-link' href="<?= base_url("outletFinishedSalesInvoice/index") ?>">
                                    <i class='fa fa-book'></i>
                                    Histori Transaksi
                                </a>
                            </li>
                        <?php endif ?>

                    <?php endif ?>
                </div>

                <?php if (Auth::check()) : ?>

                    <form method="POST" action="<?= base_url("logout") ?>">
                        <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">

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