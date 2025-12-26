<?php
/**
 * API pour générer des tokens CSRF
 * Utilisée par JavaScript pour récupérer les tokens
 */

require_once '../security_headers.php';
require_once '../session_config.php';

header('Content-Type: application/json');

require_once '../csrf.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$formName = $data['formName'] ?? 'default';

// Générer un token pour le formulaire demandé
$token = CSRF::getToken($formName);

echo json_encode([
    'success' => true,
    'token' => $token
]);
