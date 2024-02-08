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
<?php require_once('functions.php'); ?>

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

$userFavourites = getUserFavourites($mysqli, $_SESSION["userID"]);
$favouriteBooksIndexes = [];
foreach ($userFavourites as $favouriteBook) {
    $favouriteBooksIndexes[] = $favouriteBook["book_id"];
}
?>
<h1 class="text-center my-5">All books</h1>

<section class="books-container container d-flex flex-wrap justify-content-evenly">

    <?php foreach ($allBooks as $book) { ?>
        <article class="single-book m-3 d-flex flex-column justify-content-between">
            <div class="book-top px-5">

                <h4><?= $book["author"] ?></h4>
                <h5><?= $book["title"] ?></h5>
                <p><?= $book["year"] ?></p>
            </div>
            <div class="book-bottom mb-5 px-2">

                <p><?= $book["genre"] ?></p>
                <div class="d-flex align-items-center justify-content-center">
                    <p class="m-0 p-0 pe-3">Added by: </p>
                    <img class="rounded-circle" src=<?= isset($book["image_url"]) ?  $book["image_url"] : "/assets/images/avatar-1577909_960_720.webp" ?> width="35" height="35">
                </div>
                <div class="heart-container w-75 mx-auto d-flex justify-content-start">
                    <form action="controller.php" class="inner-heart" method="POST">
                        <input type="hidden" name="book-fav-id" value=<?= $book["book_id"] ?>>
                        <?php
                        $isFavourite = false;
                        if(in_array($book["book_id"], $favouriteBooksIndexes)){
                            $isFavourite = true;
                        }
                        if ($isFavourite) { ?>

                            <button class="bg-transparent border-0" type="submit" name="remove-favourite">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#C6AE5B" class="bi bi-heart-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314" />
                                </svg>
                            </button>
                        <?php } else { ?>
                            <button class="bg-transparent border-0" type="submit" name="add-favourite">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#C6AE5B" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" />
                                </svg>
                            </button>
                        <?php } ?>
                    </form>
                </div>
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