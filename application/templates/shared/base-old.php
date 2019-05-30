<?php
use App\Helpers\Auth;
use App\Enums\UserLevel;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> <?= $title ?? "Website Name" ?> </title>
    <link rel="stylesheet" href="<?= base_url("assets/app.css") ?>">
    <meta name="csrf-token" content="<?= $this->csrf_token() ?>">

    <?= $this->section('extra-styles') ?>
</head>
<body>
    <?= $this->insert('shared/navbar') ?>
    <div class="m-t:5"></div>

    <div class="container">
        <?php if(Auth::check()): ?>
        
        <div class="alert alert-info">
            <i class="fa fa-info"></i>
            Anda log in sebagai <strong> <?= Auth::user()->name ?> </strong> 
            dengan status <strong> <?= UserLevel::LEVELS[Auth::user()->level] ?? '-' ?> </strong> 
            pada outlet <strong> <?= Auth::user()->outlet->name ?? "-" ?> </strong>
        </div>

        <?php endif ?>
    </div>

    <?= $this->section('content') ?>

    <script src="<?= base_url("assets/app.js") ?>"></script>
    <?php $this->insert("shared/sweetalert-confirmation") ?>

    <?= $this->section('extra-scripts') ?>
    <?php $this->insert("shared/sentry") ?>
</body>
</html>