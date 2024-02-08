<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>


<?php require_once('database.php'); ?>

<?php require_once("partials/header.php") ?>
<?php
    session_start();
    if (isset($_SESSION["errorMsg"])) { 
        session_write_close();
        ?>
        <div class="alert alert-danger text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
            <?= $_SESSION["errorMsg"] ?>
        </div>
    <?php
    session_start();
    unset($_SESSION["errorMsg"]);
    session_write_close();
    }
    elseif (isset($_SESSION["successMsg"])) { ?>
        <div class="alert alert-success text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
            <?= $_SESSION["successMsg"] ?>
        </div>
<?php 
session_start();
unset($_SESSION["successMsg"]);
session_write_close();
}


?>

    <h1 class="text-center">Add a Book to the Library</h1>

    <form class="add-form mx-auto" action="controller.php" method="POST">
        <div class="mb-3">
            <label for="book-title" class="form-label">Book Title</label>
            <input type="text" name="book-title" class="form-control" id="book-title">
        </div>
        <div class="mb-3">
            <label for="book-author" class="form-label">Author</label>
            <input type="text" name="book-author" class="form-control" id="book-author">
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Year of publication</label>
            <input type="number" min="1800" max="<?php echo date('Y'); ?>" name="year" class="form-control" id="year">
        </div>
        <div class="mb-5">
            <label for="genre" class="form-label">Genre</label>
            <select name="genre" id="genre">
                <?php foreach ($allGenres as $genre_id => $genre) { ?>
                    <option value=<?= $genre_id ?>><?= $genre ?></option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-4" name="add-book">Add Book</button>
    </form>





<script>
    const msgBox = document.getElementById("msg-box");
    if (msgBox) {
        setTimeout(() => {
            msgBox.style.opacity = "0";
        }, 3000);
    }
</script>

<?php require_once("partials/footer.php") ?>