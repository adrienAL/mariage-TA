<?php
session_start();

require_once '../db.php';

// Mot de passe principal
$PASSWORD = "XXXXX";
// Mot de passe secret pour accéder au formulaire des trouveurs
$SECRET_PASSWORD = "c&7Xo#32-v";

$data = json_decode(file_get_contents("php://input"), true);
$input = $data['password'] ?? '';
$prenom = trim($data['prenom'] ?? '');
$nom = trim($data['nom'] ?? '');

// Vérification que nom et prénom sont fournis
if (empty($prenom) || empty($nom)) {
    echo json_encode(['success' => false, 'message' => 'Nom et prénom requis']);
    exit;
}

$isValidPassword = false;
$passwordType = 'normal';

if ($input === $PASSWORD) {
    $_SESSION['logged_in'] = true;
    $_SESSION['secret_access'] = false;
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['user_nom'] = $nom;
    $isValidPassword = true;
    $passwordType = 'normal';
} elseif ($input === $SECRET_PASSWORD) {
    $_SESSION['logged_in'] = true;
    $_SESSION['secret_access'] = true;
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['user_nom'] = $nom;
    $isValidPassword = true;
    $passwordType = 'secret';
}

if ($isValidPassword) {
    // Enregistrer le log de connexion
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt = $pdo->prepare("INSERT INTO login_logs (prenom, nom, password_type, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$prenom, $nom, $passwordType, $ip, $userAgent]);
    } catch (PDOException $e) {
        error_log("Erreur log connexion: " . $e->getMessage());
        // On continue même si le log échoue
    }
    
    echo json_encode(['success' => true, 'secret' => ($passwordType === 'secret')]);
} else {
    echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
}


