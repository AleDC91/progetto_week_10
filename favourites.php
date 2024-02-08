<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>

<?php require_once('database.php'); ?>
<?php require_once('functions.php'); ?>
<?php
session_start();

$userID = $_SESSION['userID'];

$sqlDbUserData = "SELECT * FROM users WHERE id = '$userID'";
$resDbUserData = $mysqli->query($sqlDbUserData);
if ($resDbUserData) {
    $dbUserData = [];
    while ($row = $resDbUserData->fetch_assoc()) {
        $dbUserData[] = $row;
    }
    $dbUserData = $dbUserData[0];
} else {
    echo "Errore nell'esecuzione della query: " . $mysqli->error;
}
session_write_close();
?>


<?php
$userBooks = getUserBooks($mysqli, $userID);

$userFavourites = getUserFavourites($mysqli, $userID);
$favouriteBooksIndexes = [];
foreach ($userFavourites as $favouriteBook) {
    $favouriteBooksIndexes[] = $favouriteBook["book_id"];
}

?>



<?php require_once("partials/header.php") ?>

<?php
session_start();

if (isset($_SESSION["errorMsg"])) { ?>
    <div class="alert alert-danger text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
        <?= $_SESSION["errorMsg"] ?>
    </div>
<?php unset($_SESSION["errorMsg"]);
} elseif (isset($_SESSION["successMsg"])) { ?>
    <div class="alert alert-success text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
        <?= $_SESSION["successMsg"] ?>
    </div>
<?php unset($_SESSION["successMsg"]);
    session_write_close();
}

?>



<main class="container my-5">

    <h1 class="text-center">My Favourite Books</h1>
    <?php
    if (count($userFavourites) == 0) { ?>
        <h3 class="text-center mt-5"> You don't have any favourite book yet</h3>
    <?php }; ?>





    <section class="user-booklist d-flex mt-3 flex-wrap justify-content-evenly">

        <?php
        foreach ($userFavourites as $userBook) { ?>
            <article class="user-book m-4 d-flex flex-column justify-content-between">
                <div class="book-body d-flex flex-column justify-content-between px-5">
                    <h4><?= $userBook["author"]  ?></h4>
                    <h5><?= $userBook["title"]  ?></h5>
                    <small>Year of publication: <?= $userBook["year"] ?></small>
                </div>
                <div class="buttons-book d-flex justify-content-end">
                    <div class="heart-container w-75 mx-auto d-flex justify-content-start ms-4 my-auto">
                        <form action="controller.php" class="inner-heart" method="POST">
                            <input type="hidden" name="book-fav-id" value=<?= $userBook["book_id"] ?>>
                            <?php
                            $isFavourite = false;
                            if (in_array($userBook["book_id"], $favouriteBooksIndexes)) {
                                $isFavourite = true;
                            }
                            if ($isFavourite) { ?>

                                <button class="bg-transparent border-0" type="submit" name="remove-favourite-fav">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#C6AE5B" class="bi bi-heart-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314" />
                                    </svg>
                                </button>
                            <?php } ?>

                        </form>
                    </div>

                </div>

            </article>
        <?php } ?>
    </section>
    <?php if (isset($_SESSION["edit-book-active"])) { ?>
        <div class="edit-box">
            <div class="inner-edit">

                <h2 class="text-center">Edit Book</h2>

                <form action="controller.php" method="POST" class="mt-5">
                    <div class="mb-3">
                        <label for="book-title" class="form-label">Book Title</label>
                        <input type="text" name="book-title" class="form-control" id="book-title" value="<?= $_SESSION["book-to-edit"]["title"] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="book-author" class="form-label">Author</label>
                        <input type="text" name="book-author" class="form-control" id="book-author" value="<?= $_SESSION['book-to-edit']['author'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Year of publication</label>
                        <input type="number" min="1800" max="<?php echo date('Y'); ?>" name="year" class="form-control" id="year" value=<?= $_SESSION["book-to-edit"]["year"] ?>>
                    </div>
                    <div class="mb-5">
                        <label for="genre" class="form-label">Genre</label>
                        <select name="genre" id="genre">
                            <?php foreach ($allGenres as $genre_id => $genre) { ?>
                                <?php if ($_SESSION["book-to-edit"]["genre_id"] == $genre_id) { ?>
                                    <option value="<?= $genre_id ?>" selected><?= $genre ?></option>
                                <?php } else { ?>
                                    <option value=<?= $genre_id ?>><?= $genre ?></option>
                                <?php } ?>
                            <?php } ?>

                        </select>
                    </div>
                    <input type="hidden" name="book-id" value=<?= $_SESSION["bookId"]  ?>>
                    <button type="submit" class="btn btn-warning mt-4" name="edit-book">Edit Book</button>
                    <button type="submit" class="btn btn-info mt-4" name="exit-edit">Back</button>

                </form>
                <?php print_r($_SESSION["book-to-edit"]); ?>
                <?php print_r($_SESSION["bookId"]) ?>




            </div>

        </div>

    <?php } ?>


</main>


<script>
    const msgBox = document.getElementById("msg-box");
    if (msgBox) {
        setTimeout(() => {
            msgBox.style.opacity = "0";
        }, 3000);
    }
</script>

<?php require_once("partials/footer.php") ?>