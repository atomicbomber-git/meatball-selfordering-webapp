<?php if(isset($_SESSION["message-success"])): ?>
    <div class="alert alert-success">
        <i class="fa fa-check"></i>
        <?= $_SESSION["message-success"] ?>
    </div>
<?php endif ?>

<?php if(isset($_SESSION["message-danger"])): ?>
    <div class="alert alert-danger">
        <i class="fa fa-warning"></i>
        <?= $_SESSION["message-danger"] ?>
    </div>
<?php endif ?>