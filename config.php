<?php
// config.php — personnalisable


// Mot de passe d'accès public (change le avant publication)
define('SITE_PASSWORD', 'XXXXX');


// Mail destinataire
define('DEST_EMAIL', 'ad.xxx@gmail.com');


// Paramètres MySQL (Hostinger fournit ces infos)
define('DB_HOST', 'localhost');
define('DB_NAME', 'u789089967_mariageTA');
define('DB_USER', 'u789089967_webAA');
define('DB_PASS', 'oBKs4@e43CNE?b?X');


// Options de sécurité
ini_set('display_errors', 0);
error_reporting(0);


// PDO connexion utilitaire
function getPDO(){
static $pdo = null;
if ($pdo === null) {
$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
$pdo = new PDO($dsn, DB_USER, DB_PASS, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
}
return $pdo;
}