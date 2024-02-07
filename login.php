<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php require_once('database.php') ?>

<?php require_once('partials/header.php'); ?>

<main class="container mt-5">

    <?php
    $isLogged = false;

    session_start();
    if (isset($_SESSION["isLogged"])) {
        $isLogged = true;
        header("Location: http://localhost/index.php");
    }

    if (!isset($_SESSION["msgShown"])) {
        if (isset($_SESSION["errorMsg"])) { ?>
            <div class="alert alert-danger text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
                <?= $_SESSION["errorMsg"] ?>
            </div>
    <?php }
        $_SESSION["msgShown"] = true;
    }
    ?>
    <h1 class="text-center">Login</h1>

    <form action="controller.php" method="POST" class="mt-5">

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="password">
        </div>
        <button class="btn btn-success" type="submit" name="login">Login</button>
    </form>

</main>





<script>
    const msgBox = document.getElementById("msg-box");
    if (msgBox) {
        setTimeout(() => {
            msgBox.style.opacity = "0";
        }, 3000);
    }
</script>
<?php require_once('partials/footer.php'); ?>