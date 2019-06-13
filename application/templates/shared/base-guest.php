<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> <?= $title ?? "Website Name" ?> </title>
    <link rel="stylesheet" href="<?= base_url("assets/app-guest.css") ?>">
    <meta name="csrf-token" content="<?= $this->csrf_token() ?>">
    <?= $this->section('extra-styles') ?>
</head>
<body>
    <div class="m-t:5"></div>

    <?= $this->section('content') ?>

    <script src="<?= base_url("assets/app.js") ?>"></script>
    <?= $this->section('extra-scripts') ?>

    <?php $this->insert("shared/sentry") ?>
</body>
</html>