<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
?>


<?php

$userBooks = getUserBooks($mysqli, $userID)



?>



<?php require_once("partials/header.php") ?>

<?php
if (!isset($_SESSION["msgShown"])) {
    if (isset($_SESSION["errorMsg"])) { ?>
        <div class="alert alert-danger text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
            <?= $_SESSION["errorMsg"] ?>
        </div>
    <?php } elseif (isset($_SESSION["successMsg"])) { ?>
        <div class="alert alert-success text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
            <?= $_SESSION["successMsg"] ?>
        </div>
<?php }
    $_SESSION["msgShown"] = true;
}
?>

<main class="container my-5">

    <h1 class="text-center">My Profile</h1>

    <?php
            print_r($userBooks);
            ?>
    <section class="user-info d-flex flex-column flex-lg-row mt-5 align-items-center justify-content-center">
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

    <section class="user-booklist d-flex mt-5 flex-wrap justify-content-evenly">
        <?php
        foreach ($userBooks as $userBook) { ?>
            <article class="user-book m-4">
                <h4>Title: <?= $userBook["title"]  ?></h4>
                <h5>Author: <?= $userBook["author"]  ?></h5>
                <small>Year of publication: <?= $userBook["year"] ?></small>
                <form action="controller.php" method="POST" >
                    <input type="hidden" name="book-id" value=<?= $userBook["book_id"]  ?>>
                    <button class="btn btn-danger" name="delete-book">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                        </svg>
                    </button>
                </form>
            </article>
        <?php } ?>
    </section>




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