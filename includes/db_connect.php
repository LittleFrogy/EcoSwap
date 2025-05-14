<?php
$host = getenv('DB_HOST') ?: 'localhost'; 
$db = getenv('DB_NAME') ?: 'ecoswapdb';   
$user = getenv('DB_USER') ?: 'root';      
$pass = getenv('DB_PASS') ?: '';          

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
