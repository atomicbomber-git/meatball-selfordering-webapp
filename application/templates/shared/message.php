<?php if(isset($_SESSION["message-success"])): ?>
    <div class="alert alert-success">
        <i class="fa fa-check"></i>
        <?= $_SESSION["message-success"] ?>
    </div>
<?php endif ?>