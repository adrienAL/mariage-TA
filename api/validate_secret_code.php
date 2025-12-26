<?php
header('Content-Type: application/json');

require_once '../env_loader.php';

// Code secret et message depuis les variables d'environnement
$SECRET_CODE = EnvLoader::get('SECRET_CODE');
$SECRET_MESSAGE = EnvLoader::get('PASSWORD_SECRET');

// Récupérer le hash envoyé par le client
$data = json_decode(file_get_contents("php://input"), true);
$hashReceived = $data['hash'] ?? '';

if (empty($hashReceived)) {
    echo json_encode(['success' => false, 'message' => 'Hash manquant']);
    exit;
}

// Calculer le hash du code secret côté serveur
$hashExpected = hash('sha256', $SECRET_CODE);

// Comparer les hash
if ($hashReceived === $hashExpected) {
    echo json_encode([
        'success' => true,
        'message' => $SECRET_MESSAGE
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Code incorrect'
    ]);
}
