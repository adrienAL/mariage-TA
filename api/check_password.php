<?php
session_start();

// mot de passe à changer
$PASSWORD = "XXXXX";

$data = json_decode(file_get_contents("php://input"), true);
$input = $data['password'] ?? '';

if ($input === $PASSWORD) {
    $_SESSION['logged_in'] = true;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
}
