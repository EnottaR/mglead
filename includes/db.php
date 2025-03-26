<?php
$host = "localhost";
$username = "mglead";
$password = "*204uapM5";
$dbname = "mglead";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione al database fallita: " . htmlspecialchars($conn->connect_error));
}
?>