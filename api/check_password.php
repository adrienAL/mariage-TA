<?php
session_start();

// Mot de passe principal
$PASSWORD = "XXXXX";
// Mot de passe secret pour accéder au formulaire des trouveurs
$SECRET_PASSWORD = "c&7Xo#32-v";

$data = json_decode(file_get_contents("php://input"), true);
$input = $data['password'] ?? '';

if ($input === $PASSWORD) {
    $_SESSION['logged_in'] = true;
    $_SESSION['secret_access'] = false;
    echo json_encode(['success' => true]);
} elseif ($input === $SECRET_PASSWORD) {
    $_SESSION['logged_in'] = true;
    $_SESSION['secret_access'] = true;
    echo json_encode(['success' => true, 'secret' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
}

