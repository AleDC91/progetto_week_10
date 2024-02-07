<?php
require 'vendor/autoload.php';
require_once("database.php");
require_once('functions.php');
require_once('mail.php');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


unset($_SESSION["errorMsg"]);
unset($_SESSION["successMsg"]);
unset($_SESSION["msgShown"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["register-form"])) {

        $target_dir = "uploads/";

        if (!empty($_FILES['userAvatar']['name'])) {
            $userAvatar = $_FILES['userAvatar'];
            $file_tmp = $userAvatar['tmp_name'];
            $file_name = $userAvatar['name'];
            if ($userAvatar["error"] == 0) {
                if (
                    $userAvatar["type"] == "image/jpeg" ||
                    $userAvatar["type"] == "image/png" ||
                    $userAvatar["type"] == "image/gif" ||
                    $userAvatar["type"] == "image/webp"

                ) {

                    if (move_uploaded_file($file_tmp, $target_dir . $file_name)) {
                        $imageURL = $target_dir . $file_name;
                    } else {
                        $_SESSION["errorMsg"] = "Errore nello spostamento del file, dati non inseriti";
                        header("Location: http://localhost/register.php");
                        exit();
                    }
                } else {
                    $_SESSION["errorMsg"] = "Formato file non valido";
                    header("Location: http://localhost/register.php");
                    exit();
                }
            } else {
                $_SESSION["errorMsg"] = "Errore nel caricamento del file, dati non inseriti";
                header("Location: http://localhost/register.php");
                exit();
            }
        }



        $firstName = htmlspecialchars(trim($_POST['firstName']));
        $lastName = htmlspecialchars(trim($_POST['lastName']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $userAvatar = isset($imageURL) ? $imageURL : null;


        echo $firstName . " " . $lastName . " " . $email . " " . $newsletter . " " . $password . " " . $userAvatar;

        if (strlen($firstName) < 2) {
            $_SESSION["errorMsg"] = "First name <b> " . $firstName . " </b>troppo corto";
            header("Location: http://localhost/register.php");
            exit();
        } elseif (strlen($lastName) < 2) {
            $_SESSION["errorMsg"] = "Last name <b> " . $lastName . " </b>troppo corto";
            header("Location: http://localhost/register.php");
            exit();
        } elseif (strlen($_POST['password']) < 8) {
            $_SESSION["errorMsg"] = "La password deve essere di almeno 8 caratteri";
            header("Location: http://localhost/register.php");
            exit();
        } elseif (in_array($email, $dbEmailList)) {
            $_SESSION["errorMsg"] = "Indirizzo email già presente nel database! 
                                     Inserisci una nuova email o fai il login";
            header("Location: http://localhost/register.php");
            exit();
        } else {
            addNewUser($mysqli, $firstName, $lastName, $email, $password, $newsletter, $imageURL);
        }
    }

    if (isset($_POST['logout'])) {
        session_unset();
        header("Location: http://localhost/login.php");
        exit();
    }

    if (isset($_POST['login'])) {

        unset($_SESSION["successMsg"]);
        unset($_SESSION["errorMsg"]);

        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["errorMsg"] = "Invalid email";
            header("Location: http://localhost/login.php");
            exit();
        }
        if (strlen($password) < 8) {
            $_SESSION["errorMsg"] = "Password deve essere di almeno 8 caratteri";
            header("Location: http://localhost/login.php");
            exit();
        }

        $loggedMatch = false;
        foreach ($allUsers as $user) {
            if ($user["email"] == $email && password_verify($password, $user["password"])) {
                $loggedMatch = true;

                $_SESSION["isLogged"] = true;
                $_SESSION["userName"] = $user["first_name"];
                $_SESSION["lastName"] = $user["last_name"];
                $_SESSION["userEmail"] = $user["email"];
                $_SESSION["userImage"] = $user["image_url"];
                $_SESSION["userID"] = $user["id"];
                break;
            }
        }
        if ($loggedMatch) {
            unset($_SESSION["errorMsg"]);
            header("Location: http://localhost/index.php");
            exit();
        } else {
            $_SESSION["errorMsg"] = "Email o password errati";
            header("Location: http://localhost/login.php");
            exit();
        }
    }

    if (isset($_POST["add-book"])) {

        $bookTitle = htmlspecialchars(trim($_POST["book-title"]));
        $bookAuthor = htmlspecialchars(trim($_POST["book-author"]));
        $bookYear = filter_var(filter_var($_POST["year"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        $bookGenre =  filter_var(filter_var($_POST["genre"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        $addedBy = $_SESSION["userID"];


        if (!is_string($bookTitle) || strlen($bookTitle) < 0) {
            $_SESSION["errorMsg"] = "Inserisci il titolo del libro!";
            header("Location: http://localhost/addBooks.php");
            exit();
        } elseif (!is_string($bookAuthor) || strlen($bookAuthor) < 0) {
            $_SESSION["errorMsg"] = "Inserisci il titolo del libro!";
            header("Location: http://localhost/addBooks.php");
            exit();
        } elseif (!is_int($bookYear) || $bookYear < 1800 || $bookYear > date('Y')) {
            $_SESSION["errorMsg"] = "Data non valida!!";
            header("Location: http://localhost/addBooks.php");
            exit();
        } elseif (!is_int($bookGenre) || $book < 0 || $bookGenre > count($allGenres)) {
            $_SESSION["errorMsg"] = "Genere inserito non valido!!" . $bookGenre;
            header("Location: http://localhost/addBooks.php");
            exit();
        } else {


            addNewBook($mysqli, $bookTitle, $bookAuthor, $bookYear, $bookGenre, $addedBy);
        }
    }

    if (isset($_POST["delete-book"])){
        $bookID = filter_var(filter_var($_POST["book_id"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        $userID = $_SESSION["userID"];
        $allUserBooks = getUserBooks($mysqli, $userId);

        foreach ($allUserBooks as $userBook){
            if (!$userBook["book_id"] == $bookID){
                $_SESSION["errorMsg"] = "Non puoi eliminare libri aggiunti da altri";
                header("Location: http://localhost/profile.php");
                exit();
            }
        }

        $sqlDeleteBook = "DELETE FROM books WHERE book_id = '$bookID'";
        if(!$mysqli->query($sqlDeleteBook)){
            $_SESSION["errorMsg"] = "Errore nella cancellazione del libro";
            header("Location: http://localhost/profile.php");
            exit();

        } else {
            $_SESSION["successMsg"] = "Libro rimosso dal database con successo";
            header("Location: http://localhost/profile.php");
            exit();
        }
        
    }
}
