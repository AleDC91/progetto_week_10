
<!-- 
Inserire i parametri per la connessione al DB nel file config.php.

Si possono personalizzare i parametri di mailtrap nel file mail.php.

L'appllicazione permette di registrarsi e fare il login. Una volta registrati, 
manda un'email di benvenuto al nuovo utente. Il database viene popolato al primo avvio 
con 4 utenti fake e qualche libro. Ogni utente, dalla sezione profilo, ha la possibilità 
di modificare o eliminare dal database solamente i libri da lui inseriti. Nella sezione allBooks 
sono visibili tutti i libri caricati dagli utenti. Cliccando sull'avatar nel libro si accede ad 
una semplice pagina di dettaglio dell'utente che ha aggiunto quel libro. I libri nella sezione 
principale possono essere filtrati, indifferentemente per autore o titolo. 
Cliccando sull'icona del cuore in copertina, si può aggiungere (o rimuovere) un libro alla lista
dei preferiti. Tutti i dati inseriti dall'utente sono controllati lato server, e vengono visualizzati 
messaggi di errore o di successo a seconda dell'esito delle operazioni effettuate.

In questo esercizio ho sperimentato divesi modi per interagire con il database. 
Sempre son mysqli, qualche richiesta è stata fatta usando anche i prepared statements.
Con le tabelle 'genres' e 'favourites' ho usato le relazioni uno a molti e molti a molti.

  
 -->






<?php
require 'vendor/autoload.php';
require_once("database.php");
require_once('functions.php');
require_once('mail.php');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();


unset($_SESSION["book-to-edit"]);


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    unset($_SESSION["errorMsg"]);
    unset($_SESSION["successMsg"]);


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


        if (!is_string($bookTitle) || strlen($bookTitle) < 1) {
            $_SESSION["errorMsg"] = "Inserisci il titolo del libro!";
            header("Location: http://localhost/addBooks.php");
            exit();
        } elseif (!is_string($bookAuthor) || strlen($bookAuthor) < 1) {
            $_SESSION["errorMsg"] = "Inserisci l'autore del libro!";
            header("Location: http://localhost/addBooks.php");
            exit();
        } elseif (!is_int($bookYear) || $bookYear < 1800 || $bookYear > date('Y')) {
            $_SESSION["errorMsg"] = "Data non valida!!";
            header("Location: http://localhost/addBooks.php");
            exit();
        } elseif (!is_int($bookGenre) || $bookGenre < 1 || $bookGenre > count($allGenres)) {
            $_SESSION["errorMsg"] = "Genere inserito non valido!!" . $bookGenre;
            header("Location: http://localhost/addBooks.php");
            exit();
        } else {


            addNewBook($mysqli, $bookTitle, $bookAuthor, $bookYear, $bookGenre, $addedBy);
        }
    }

    if (isset($_POST["delete-book"])) {

        $bookID = filter_var(filter_var($_POST["book-id"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        $userID = $_SESSION["userID"];

        deleteUserBook($mysqli, $bookID, $allUserBooks, $userID);
    }

    if (isset($_POST["open-edit-book"])) {
        $_SESSION["edit-book-active"] = true;
        $bookId = filter_var(filter_var($_POST["book-id"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        $_SESSION["bookId"] = $bookId;
        $_SESSION["book-to-edit"] = getUserBook($mysqli, $_SESSION["userID"], $_SESSION["bookId"]);
        print_r($_SESSION["book-to-edit"]);
        header("Location: http://localhost/profile.php");
        exit();
    }

    if (isset($_POST["edit-book"])) {

        unset($_SESSION["edit-book-active"]);
        $userID = $_SESSION["userID"];
        $newTitle = htmlspecialchars(trim($_POST["book-title"]));
        $newAuthor = htmlspecialchars(trim($_POST["book-author"]));
        $newYear = filter_var(filter_var($_POST["year"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        $newGenre =  filter_var(filter_var($_POST["genre"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        $BOOKID = filter_var(filter_var($_POST["book-id"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);


        if (!is_string($newTitle) || strlen($newTitle) < 1) {
            $_SESSION["errorMsg"] = "Inserisci il titolo del libro!";
            header("Location: http://localhost/profile.php");
            exit();
        } elseif (!is_string($newAuthor) || strlen($newAuthor) < 1) {
            $_SESSION["errorMsg"] = "Inserisci l'autore del libro!";
            header("Location: http://localhost/profile.php");
            exit();
        } elseif (!is_int($newYear) || $newYear < 1800 || $newYear > date('Y')) {
            $_SESSION["errorMsg"] = "Data non valida!!";
            header("Location: http://localhost/profile.php");
            exit();
        } elseif (!is_int($newGenre) || $newGenre < 1 || $newGenre > count($allGenres)) {
            $_SESSION["errorMsg"] = "Genere inserito non valido!!" . $bookGenre;
            header("Location: http://localhost/profile.php");
            exit();
        } else {
            echo $userID . " " . $newTitle . " " . $newAuthor . " " . $newYear . " " . $newGenre;
            editBook($mysqli, $newTitle, $newAuthor, $newYear, $newGenre, $BOOKID);
        }
    }
    if (isset($_POST["exit-edit"])) {
        unset($_SESSION["book-to-edit"]);

        unset($_SESSION["edit-book-active"]);
        header("Location: http://localhost/profile.php");
        exit();
    }

    if (isset($_POST["add-favourite"])) {
        $userId = $_SESSION["userID"];
        $bookId = filter_var(filter_var($_POST["book-fav-id"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        addFavourite($mysqli, $bookId, $userId);
        header("Location: http://localhost/index.php");
        exit();
    }


    if (isset($_POST["remove-favourite"])) {
        $userId = $_SESSION["userID"];
        $bookId = filter_var(filter_var($_POST["book-fav-id"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        removeFavourites($mysqli, $bookId, $userId);
        header("Location: http://localhost/index.php");
        exit();
    }


    if (isset($_POST["remove-favourite-fav"])) {
        $userId = $_SESSION["userID"];
        $bookId = filter_var(filter_var($_POST["book-fav-id"], FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        removeFavourites($mysqli, $bookId, $userId);
        header("Location: http://localhost/favourites.php");
        exit();
    }


    if (isset($_POST["query"])) {
        $_SESSION["query"] = htmlspecialchars($_POST["query"]);
        echo  $_POST["query"];
        header("Location: http://localhost/index.php");
        exit();
    }
}
