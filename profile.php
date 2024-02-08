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
session_write_close();
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
?>


<?php

$userBooks = getUserBooks($mysqli, $userID)



?>



<?php require_once("partials/header.php") ?>

<?php
session_start();
if (isset($_SESSION["errorMsg"])) {
 ?>
    <div class="alert alert-danger text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
        <?= $_SESSION["errorMsg"] ?>
    </div>
<?php

unset($_SESSION["errorMsg"]);

}elseif (isset($_SESSION["successMsg"])) { ?>
    <div class="alert alert-success text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
        <?= $_SESSION["successMsg"] ?>
    </div>
<?php 

unset($_SESSION["successMsg"]);
session_write_close();
}


?>



<main class="container my-5">

    <h1 class="text-center">My Profile</h1>

    <?php
    // print_r($userBooks);
    // print_r($allUserBooks);
    // print_r($_SESSION["userID"]);

    ?>
    <section class="user-info d-flex flex-column flex-lg-row my-5 align-items-center justify-content-center">
        <div class="user-info-avatar mx-5 my-4 my-lg-0">
            <img class="img-fluid rounded-circle w-100 h-100" src=<?= isset($dbUserData["image_url"]) ? $dbUserData["image_url"] : "assets/images/avatar-1577909_960_720.webp"  ?> alt="avatar">
        </div>
        <div class="user-info-data">
            <h4>First Name: <?= $dbUserData["first_name"] ?></h4>
            <h4>Last Name: <?= $dbUserData["last_name"] ?></h4>
            <h4>Email: <?= $dbUserData["email"] ?></h4>
            <h4>Added books: <?= count($userBooks); ?></h4>
        </div>
    </section>

    <h2 class="text-center pt-5">My books</h2>
    <section class="user-booklist d-flex mt-3 flex-wrap justify-content-evenly">

        <?php
        foreach ($userBooks as $userBook) { ?>
            <article class="user-book m-4 d-flex flex-column justify-content-between">
                <div class="book-body d-flex flex-column justify-content-between px-5">
                    <h4><?= $userBook["author"]  ?></h4>
                    <h5><?= $userBook["title"]  ?></h5>
                    <small>Year of publication: <?= $userBook["year"] ?></small>
                </div>
                <div class="buttons-book d-flex justify-content-end">

                    <form action="controller.php" method="POST">
                        <input type="hidden" name="book-id" value=<?= $userBook["book_id"]  ?>>

                        <button type="submit" class="btn btn-edit" name="open-edit-book">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                            </svg>
                        </button>
                    </form>
                    <form action="controller.php" method="POST" class="mx-3">
                        <input type="hidden" name="book-id" value=<?= $userBook["book_id"]  ?>>
                        <button type="submit" class="btn btn-delete" name="delete-book">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                            </svg>
                        </button>
                    </form>
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