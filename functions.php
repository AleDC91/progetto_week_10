<?php 
function getIdFromEmail($mysqli, $email)
{
    $sql_select = "SELECT id FROM users WHERE email = ?";
    $stmt_select = $mysqli->prepare($sql_select);
    $stmt_select->bind_param("s", $email);
    if ($stmt_select->execute()) {
        $result = null;
        $stmt_select->bind_result($result);
        if ($stmt_select->fetch()) {

            $stmt_select->close();
            return $result;
        }
    }
}

function addNewUser($mysqli, $firstName, $lastName, $email, $password, $newsletter, $imageURL)
{
    $sql = "INSERT INTO users (first_name, last_name, email, password, newsletter, image_url) 
    VALUES (?, ?, ?, ?, ?, ?);";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        $_SESSION["errorMsg"] = "Errore durante la preparazione della query: " . $mysqli->error;
        header("Location: http://localhost/register.php");
        exit();
    }

    $stmt->bind_param("ssssss", $firstName, $lastName, $email, $password, $newsletter, $imageURL);

    if (!$stmt->execute()) {
        $_SESSION["errorMsg"] = "Errore # " . $mysqli->errno . " " . "Errore! " . $mysqli->error;
        header("Location: http://localhost/register.php");
        exit();
    } else {
        getIdFromEmail($mysqli, $email);
        $_SESSION["userID"] = getIdFromEmail($mysqli, $email);
        $_SESSION["successMsg"] = "Nuovo utente registrato sul database";
        $_SESSION["mailLoggedUser"] = $email;
        $_SESSION["userName"] = $firstName;
        $_SESSION["isLogged"] = true;
        $_SESSION["lastName"] = $lastName;
        $_SESSION["userImage"] = $imageURL;

        // manda email di benvenuto
        sendWelcomeEmail($email, $firstName, $lastName);

        header("Location: http://localhost/login.php");
        exit();
    }
}

function addNewBook($mysqli, $bookTitle, $bookAuthor, $bookYear, $bookGenre, $addedBy){
    $sqlAddBook = "INSERT INTO books (title, author, year, genre_id, added_by) VALUES ('$bookTitle', '$bookAuthor', '$bookYear', '$bookGenre', '$addedBy')";
    if (!$mysqli->query($sqlAddBook)) {
        $_SESSION["errorMsg"] = "Errore nel caricamento del libro nel database" . $mysqli->error;
        header("Location: http://localhost/addBooks.php");
        exit();
    } else {
        $_SESSION["successMsg"] = "Nuovo libro aggiunto al database";
        header("Location: http://localhost/addBooks.php");
        exit();
    }
}

function getUserBooks($mysqli, $userId){
    $sqlUserBooks = "SELECT * FROM books JOIN users WHERE books.added_by = users.id AND books.added_by = '$userId'";
    $resUserBooks = $mysqli->query($sqlUserBooks);
    if ($resUserBooks) {
        $userBooks = [];
        while ($row = $resUserBooks->fetch_assoc()) {
            $userBooks[] = $row;
        }
        return $userBooks;
    } else {
        echo "Errore nell'esecuzione della query: " . $mysqli->error;
    }
}
?>