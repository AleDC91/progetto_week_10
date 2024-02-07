<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$isLogged = false;

session_start();
if (!isset($_SESSION["isLogged"])) {
    $isLogged = true;
    header("Location: http://localhost/login.php");
}
?>

<?php require_once('database.php'); ?>
<?php require_once("partials/header.php") ?>

<?php
if (!isset($_SESSION["msgShown"])) {
    if (isset($_SESSION["successMsg"])) { ?>
        <div class="alert alert-success text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
            <?= $_SESSION["successMsg"] ?>
        </div>
<?php }
    unset($_SESSION["msgShown"]);
}
?>


    <h1 class="text-center my-5">All books</h1>

    <section class="books-container container d-flex flex-wrap justify-content-evenly">

        <?php foreach ($allBooks as $book) { ?>
            <article class="single-book m-3">
                <h2><?= $book["title"] ?></h2>
                <h3><?= $book["author"] ?></h3>
                <p><?= $book["year"] ?></p>
                <p><?= $book["genre"] ?></p>
                <div  class="d-flex align-items-center justify-content-center">
                    <p class="m-0 p-0 pe-3">Added by: </p>
                    <img class="rounded-circle" src=<?= isset($book["image_url"]) ?  $book["image_url"] : "/assets/images/avatar-1577909_960_720.webp" ?> width="35" height="35">
                </div>
            </article>
        <?php } ?>

    </section>











<script>
    const msgBox = document.getElementById("msg-box");
    if (msgBox) {
        setTimeout(() => {
            msgBox.style.opacity = "0";
        }, 3000);
    }
</script>
<?php require_once("partials/footer.php") ?>