<?php
$DB_HOST = 'localhost';
$DB_NAME = 'u789089967_mariageTA';
$DB_USER = 'u789089967_webAA';
$DB_PASS = 'oBKs4@e43CNE?b?X'; // à mettre

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base : " . $e->getMessage());
}
