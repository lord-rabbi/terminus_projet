<?php

$host = 'localhost';
$dbname = 'gestion_presence';
$username = 'root';
$password = 'Root1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<?php 

$con = mysqli_connect("localhost", "root", "Root1234", "gestion_presence");
if (!$con) {
    die('Error:' . mysqli_connect_error());
}
?>