<?php
header('Content-Type: application/json');

// Code secret et message (stockés côté serveur uniquement)
$SECRET_CODE = '4815162342';
$SECRET_MESSAGE = 'c&7Xo#32-v';

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
