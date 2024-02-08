<?php
require_once('config.php');


// CONNESSIONE AL DB

$mysqli = new mysqli(
    $config["dbHost"],
    $config["dbUser"],
    $config["dbPass"]
);

if ($mysqli->connect_error) {
    die("Error connecting to database: " . $mysqli->connect_error);
}

// CREAZIONE DEL DB

$sql = "CREATE DATABASE IF NOT EXISTS progetto_week_10_ADC";

if (!$mysqli->query($sql)) {
    die("Error creating database: " . $mysqli->error);
}

$sql = "USE progetto_week_10_ADC";

if (!$mysqli->query($sql)) {
    die("Error: " . $mysqli->error);
}


// CREAZIONE TABELLE

$sqlTable = "CREATE TABLE IF NOT EXISTS users (
 `id` INT NOT NULL AUTO_INCREMENT , 
 `first_name` VARCHAR(64) NOT NULL , 
 `last_name` VARCHAR(64) NOT NULL , 
 `email` VARCHAR(64) NOT NULL , 
 `password` VARCHAR(255) NOT NULL , 
 `newsletter` BOOLEAN NOT NULL , 
 `image_url` VARCHAR(255) NULL , 
 PRIMARY KEY (`id`), 
 UNIQUE (`email`))
";

if (!$mysqli->query($sqlTable)) {
    die("Errore nella creazione della tabella users: " . $mysqli->error);
}

$sqlGenresTable = "CREATE TABLE IF NOT EXISTS genres (
    `genre_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `genre` VARCHAR(255) NOT NULL)";

if (!$mysqli->query($sqlGenresTable)) {
    die("Errore nella creazione della tabella genres: " . $mysqli->error);
}


$sqlBooksTable = "CREATE TABLE IF NOT EXISTS books (
    `book_id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `year` INT,
    `genre_id` INT NOT NULL,
    `added_by` INT NOT NULL,
    PRIMARY KEY (`book_id`),
    CONSTRAINT `FK_books_genres` 
    FOREIGN KEY (`genre_id`) REFERENCES genres(`genre_id`),
    FOREIGN KEY (`added_by`) REFERENCES users(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
   ) ";

if (!$mysqli->query($sqlBooksTable)) {
    die("Errore nella creazione della tabella books: " . $mysqli->error);
}






$sqlFavouritesTable = "CREATE TABLE IF NOT EXISTS favourites (
    `user_id` INT,
    `book_id` INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE

    )";

if (!$mysqli->query($sqlFavouritesTable)) {
    die("Errore nella creazione della tabella favourites: " . $mysqli->error);
}




// RIEMPI TABELLA GENERI

// controlla se è già popolata, altrimenti riempi
$checkSql = "SELECT COUNT(*) as count FROM genres";
$result = $mysqli->query($checkSql);
$row = $result->fetch_assoc();
$countRowsInGenres = $row['count'];

if ($countRowsInGenres == 0) {
foreach ($genresList as $key => $value) {
    $sql = "INSERT INTO genres (genre_id, genre) VALUES ($key, '" . $mysqli->real_escape_string($value) . "')";
    if (!$mysqli->query($sql)) {
        echo "Errore nell'inserimento dei dati nella tabella genres: " . $mysqli->error;
    } else {
        echo "Tabella genres riempita con successo";
    }
}
}

// ESTRAZIONE ARRAY UTILI

$dbEmailList = [];
$sqlEmail = "SELECT email FROM users";
$resEmail = $mysqli->query($sqlEmail);

if ($resEmail) {
    while ($row = $resEmail->fetch_assoc()) {
        $dbEmailList[] = $row["email"];
    }
}


$sqlAll = "SELECT * FROM users";
$allUsers = [];
$res = $mysqli->query($sqlAll);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $allUsers[] = $row;
    }
}


$sqlAllBooks = "SELECT * FROM books JOIN genres ON books.genre_id = genres.genre_id JOIN users ON books.added_by = users.id";
$allBooks = [];
$resAllBooks = $mysqli->query($sqlAllBooks);
if ($resAllBooks) {
    while ($row = $resAllBooks->fetch_assoc()) {
        $allBooks[] = $row;
    }
}


$sqlAllGenres = "SELECT * FROM genres";
$allGenres = [];
$resAllGenres = $mysqli->query($sqlAllGenres);
if ($resAllGenres) {
    while ($row = $resAllGenres->fetch_assoc()) {
        $allGenres[$row["genre_id"]] = $row["genre"];
    }
}