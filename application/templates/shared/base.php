<?php
use App\Helpers\Auth;
use App\Helpers\AppInfo;
use App\Policies\OutletPolicy;
use App\Policies\UserPolicy;
use App\Policies\MenuCategoryPolicy;
use App\Policies\OutletMenuPolicy;
use App\Policies\DiscountPolicy;

defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1" />
    <meta name="msapplication-tap-highlight" content="no" />

    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="application-name" content="appname" />

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <meta name="theme-color" content="#4C7FF0" />

    <title>
        <?= AppInfo::name() ?> | <?= $title ?? '-' ?> 
    </title>

    <!-- page stylesheets -->
    <!-- end page stylesheets -->

    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/dist/css/bootstrap.css') ?> " />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/PACE/themes/blue/pace-theme-minimal.css') ?> " />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/font-awesome/css/font-awesome.css') ?> " />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/animate.css/animate.css') ?> " />
    <link rel="stylesheet" href="<?= base_url('assets/styles/app.css') ?>" id="load_styles_before" />
    <link rel="stylesheet" href="<?= base_url('assets/styles/app.skins.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/app.css') ?>" />

    <?= $this->section('extra-styles') ?>
    <meta name="csrf-token" content="<?= $this->csrf_token() ?>">
</head>

<body>
    <div class="app">
        <!--sidebar panel-->
        <div class="off-canvas-overlay" data-toggle="sidebar"></div>

        <div class="sidebar-panel">
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

                    <form method="POST" action="<?= base_url("logout/handle") ?>">
                        <input type="hidden" name="<?= $this->csrf_name() ?>" value="<?= $this->csrf_token() ?>">
                    </form>
                </div>
            </div>
            <!-- main navigation -->
            
            <?php $this->insert("shared/navmenu") ?>
            
            <!-- /main navigation -->
        </div>

        <!-- /sidebar panel -->
        <!-- content panel -->
        <div class="main-panel">
            <!-- top header -->
            <nav class="header navbar">
                <div class="header-inner">
                    <div class="navbar-item navbar-spacer-right brand hidden-lg-up">
                        <!-- toggle offscreen menu -->
                        <a href="javascript:;" data-toggle="sidebar" class="toggle-offscreen">
                            <i class="material-icons">menu</i>
                        </a>
                        <!-- /toggle offscreen menu -->
                        <!-- logo -->
                        <a class="brand-logo hidden-xs-down">
                            <img src="images/logo_white.png" alt="logo" />
                        </a>
                        <!-- /logo -->
                    </div>
                    <a class="navbar-item navbar-spacer-right navbar-heading hidden-md-down" href="">
                        <span>
                            <?= AppInfo::name() ?> | <?= $title ?? '-' ?>
                        </span>
                    </a>
                    <!-- <div class="navbar-search navbar-item">
                            <form class="search-form">
                                <i class="material-icons">search</i>
                                <input
                                    class="form-control"
                                    type="text"
                                    placeholder="Search"
                                />
                            </form>
                        </div> -->
                    <div class="navbar-item nav navbar-nav">
                        <!-- <div class="nav-item nav-link dropdown">
                                <a
                                    href="javascript:;"
                                    class="dropdown-toggle"
                                    data-toggle="dropdown"
                                >
                                    <span>English</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="javascript:;"
                                        >English</a
                                    >
                                    <a class="dropdown-item" href="javascript:;"
                                        >Russian</a
                                    >
                                </div>
                            </div> -->

                        <!-- NOTIFICAIONS -->

                        <!-- <div class="nav-item nav-link dropdown">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="material-icons">notifications</i>
                                <span class="tag tag-danger">4</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right notifications">
                                <div class="dropdown-item">
                                    <div class="notifications-wrapper">
                                        <ul class="notifications-list">
                                            <li>
                                                <a href="javascript:;">
                                                    <div class="notification-icon">
                                                        <div class="circle-icon bg-success text-white">
                                                            <i class="material-icons">arrow_upward</i>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message">
                                                        <b>Sean</b>
                                                        launched a new
                                                        application
                                                        <span class="time">2 seconds
                                                            ago</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;">
                                                    <div class="notification-icon">
                                                        <div class="circle-icon bg-danger text-white">
                                                            <i class="material-icons">check</i>
                                                        </div>
                                                    </div>
                                                    <div class="notification-message">
                                                        <b>Removed
                                                            calendar</b>
                                                        from app list
                                                        <span class="time">4 hours
                                                            ago</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;">
                                                    <span class="notification-icon">
                                                        <span class="circle-icon bg-info text-white">J</span>
                                                    </span>
                                                    <div class="notification-message">
                                                        <b>Jack Hunt</b>
                                                        has
                                                        <b>joined</b>
                                                        mailing list
                                                        <span class="time">9 days
                                                            ago</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;">
                                                    <span class="notification-icon">
                                                        <span class="circle-icon bg-primary text-white">C</span>
                                                    </span>
                                                    <div class="notification-message">
                                                        <b>Conan Johns</b>
                                                        created a new list
                                                        <span class="time">9 days
                                                            ago</span>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="notification-footer">
                                        Notifications
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <!-- <a
                                href="javascript:;"
                                class="nav-item nav-link nav-link-icon"
                                data-toggle="modal"
                                data-target=".chat-panel"
                                data-backdrop="false"
                            >
                                <i class="material-icons">chat_bubble</i>
                            </a> -->
                    </div>
                </div>
            </nav>
            <!-- /top header -->

            <!-- main area -->
            <div class="main-content">
                <div class="content-view">
                    <?= $this->section('content') ?>
                </div>
                <!-- bottom footer -->
                <div class="content-footer">
                    <nav class="footer-right">
                        <ul class="nav">
                            <li>
                                <a href="javascript:;">Feedback</a>
                            </li>
                        </ul>
                    </nav>
                    <nav class="footer-left">
                        <ul class="nav">
                            <li>
                                <a href="javascript:;">
                                    <span>Copyright</span>
                                    &copy;
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <!-- /bottom footer -->
            </div>
            <!-- /main area -->
        </div>
        <!-- /content panel -->

        <!--Chat panel-->
        <div class="modal fade sidebar-modal chat-panel" tabindex="-1" role="dialog" aria-labelledby="chat-panel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="chat-header">
                        <a class="pull-right" href="javascript:;" data-dismiss="modal">
                            <i class="material-icons">close</i>
                        </a>
                        <div class="chat-header-title">People</div>
                    </div>
                    <div class="modal-body flex scroll-y">
                        <div class="chat-group">
                            <div class="chat-group-header">Favourites</div>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-online"></span>
                                <span>Catherine Moreno</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-online"></span>
                                <span>Denise Peterson</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-away"></span>
                                <span>Charles Wilson</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-away"></span>
                                <span>Melissa Welch</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-no-disturb"></span>
                                <span>Vincent Peterson</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Pamela Wood</span>
                            </a>
                        </div>
                        <div class="chat-group">
                            <div class="chat-group-header">Online</div>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-online"></span>
                                <span>Tammy Carpenter</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-away"></span>
                                <span>Emma Sullivan</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-no-disturb"></span>
                                <span>Andrea Brewer</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-online"></span>
                                <span>Betty Simmons</span>
                            </a>
                        </div>
                        <div class="chat-group">
                            <div class="chat-group-header">Other</div>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Denise Peterson</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Jose Rivera</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Diana Robertson</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>William Fields</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Emily Stanley</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Jack Hunt</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Sharon Rice</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Mary Holland</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Diane Hughes</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Steven Smith</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Emily Henderson</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Wayne Kelly</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Jane Garcia</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Jose Jimenez</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Rachel Burton</span>
                            </a>
                            <a href="javascript:;" data-toggle="modal" data-target=".chat-message">
                                <span class="status-offline"></span>
                                <span>Samantha Ruiz</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade chat-message" tabindex="-1" role="dialog" aria-labelledby="chat-message" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="chat-header">
                        <div class="pull-right dropdown">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" href="javascript:;">Profile</a>
                                <a class="dropdown-item" href="javascript:;">Clear messages</a>
                                <a class="dropdown-item" href="javascript:;">Delete conversation</a>
                                <a class="dropdown-item" href="javascript:;" data-dismiss="modal">Close chat</a>
                            </div>
                        </div>
                        <div class="chat-conversation-title">
                            <img src="images/face1.jpg" class="avatar avatar-xs img-circle m-r-1 pull-left" alt="" />
                            <div class="overflow-hidden">
                                <span><strong>Charles Wilson</strong></span>
                                <small>Last seen today at 03:11</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body flex scroll-y">
                        <p class="text-xs-center text-muted small text-uppercase bold m-b-1">
                            Yesterday
                        </p>
                        <div class="chat-conversation-user them">
                            <div class="chat-conversation-message">
                                <p>Hey.</p>
                            </div>
                        </div>
                        <div class="chat-conversation-user them">
                            <div class="chat-conversation-message">
                                <p>How are the wife and kids, Taylor?</p>
                            </div>
                        </div>
                        <div class="chat-conversation-user me">
                            <div class="chat-conversation-message">
                                <p>Pretty good, Samuel.</p>
                            </div>
                        </div>
                        <p class="text-xs-center text-muted small text-uppercase bold m-b-1">
                            Today
                        </p>
                        <div class="chat-conversation-user them">
                            <div class="chat-conversation-message">
                                <p>Curabitur blandit tempus porttitor.</p>
                            </div>
                        </div>
                        <div class="chat-conversation-user me">
                            <div class="chat-conversation-message">
                                <p>Goodnight!</p>
                            </div>
                        </div>
                        <div class="chat-conversation-user them">
                            <div class="chat-conversation-message">
                                <p>
                                    Duis mollis, est non commodo luctus,
                                    nisi erat porttitor ligula, eget lacinia
                                    odio sem nec elit.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="chat-conversation-footer">
                        <button class="chat-left">
                            <i class="material-icons">face</i>
                        </button>
                        <div class="chat-input" contenteditable=""></div>
                        <button class="chat-right">
                            <i class="material-icons">photo</i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--/Chat panel-->
    </div>

    <script type="text/javascript">
        window.paceOptions = {
            document: true,
            eventLag: true,
            restartOnPushState: true,
            restartOnRequestAfter: true,
            ajax: {
                trackMethods: ["POST", "GET"]
            }
        };
    </script>

    <script src="<?= base_url('assets/vendor/jquery/dist/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/PACE/pace.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/tether/dist/js/tether.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/dist/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/fastclick/lib/fastclick.js') ?>"></script>
    <script src="<?= base_url('assets/scripts/constants.js') ?>"></script>
    <script src="<?= base_url('assets/scripts/main.js') ?>"></script>
    <script src="<?= base_url('assets/app.js') ?>"></script>



    <script>
        // Handles logout button click
        $('#logout_button').click(function() {
            $('#logout_form').submit();
        });
    </script>

    <?php $this->insert("shared/sweetalert-confirmation") ?>

    <?= $this->section('extra-scripts') ?>
</body>

</html>