<?php
session_start();
$username = isset($_SESSION["userName"]) ? $_SESSION["userName"] : "guest";
$isLogged = isset($_SESSION["isLogged"]) && $_SESSION["isLogged"];
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Libreria</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav w-100 <?php if($isLogged){ ?>justify-content-between<?php } else { ?>justify-content-end<?php } ?>">
                <div class=" nav-items-left d-flex flex-column flex-lg-row ">

                    <?php
                    if ($isLogged) { ?>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="addBooks.php">Add Books</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="favourites.php">My Favourites</a>
                        </li>
                        <p class="p-0 m-0 ms-lg-5 saluto">
                            <?php echo "Hi, " . $_SESSION["userName"] . "!" ?>
                        </p>

                    
                </div>
                <div class="d-lg-flex">
                    <li class="nav-item dropdown me-5">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src=<?= isset($_SESSION["userImage"]) ? $_SESSION["userImage"] : "assets/images/avatar-1577909_960_720.webp";  ?> alt="avatar" style="max-width: 50px; max-heigth: 50px">
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="index.php">Books</a></li>
                            <li><a class="dropdown-item" href="addBooks.php">Add Books</a></li>
                            <li><a class="dropdown-item" href="favourites.php">My Favourites</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="controller.php" method="post" class="text-center">
                                    <button class="btn btn-dark" name="logout">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php

                    if ($isLogged) { ?>
                        <form action="controller.php" method="post" class="mx-2 my-auto">
                            <button class="btn btn-dark" name="logout">Logout</button>
                        </form>
                    <?php } ?>

                    <?php if (!$isLogged && $currentPage == "login.php") { ?>
                        <a href="register.php"> <button class="btn btn-dark my-auto">Register</button></a>
                    <?php } ?>

                    <?php if (!$isLogged && $currentPage != "login.php") { ?>
                        <a href="login.php"> <button class="btn btn-dark my-auto">Login</button></a>
                    <?php } ?>
                </div>


            </ul>
        </div>
    </div>
</nav>
<?php session_write_close(); ?>
