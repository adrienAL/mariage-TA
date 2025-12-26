<?php
require_once __DIR__ . '/env_loader.php';

$DB_HOST = EnvLoader::get('DB_HOST', 'localhost');
$DB_NAME = EnvLoader::get('DB_NAME');
$DB_USER = EnvLoader::get('DB_USER');
$DB_PASS = EnvLoader::get('DB_PASS');

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base : " . $e->getMessage());
    die("Erreur de connexion à la base de données. Contactez l'administrateur.");
}
