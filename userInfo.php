<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>

<?php require_once('database.php'); ?>
<?php require_once('functions.php'); ?>
<?php
session_start();

$user = $_GET['user'];

$sqlDbUserData = "SELECT * FROM users WHERE id = '$user'";
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





<?php require_once("partials/header.php") ?>




<h1 class="text-center">User Detail</h1>

<section class="user-info d-flex flex-column flex-lg-row my-5 align-items-center justify-content-center">
    <div class="user-info-avatar mx-5 my-4 my-lg-0">
        <img class="img-fluid rounded-circle w-100 h-100" src=<?= isset($dbUserData["image_url"]) ? $dbUserData["image_url"] : "assets/images/avatar-1577909_960_720.webp"  ?> alt="avatar" width="200" height="200">
    </div>
    <div class="user-info-data">
        <h4>First Name: <?= $dbUserData["first_name"] ?></h4>
        <h4>Last Name: <?= $dbUserData["last_name"] ?></h4>
        <h4>Email: <?= $dbUserData["email"] ?></h4>
    </div>
</section>




</main>



<?php require_once("partials/footer.php") ?>