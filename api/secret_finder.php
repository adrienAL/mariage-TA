<?php
header('Content-Type: application/json');
require_once '../security_headers.php';
require_once '../session_config.php';

require_once '../csrf.php';

// Vérifier que l'utilisateur a accès à la zone secrète
if (!isset($_SESSION['secret_access']) || $_SESSION['secret_access'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

// Valider le token CSRF
if (!CSRF::validateRequest('secret')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de sécurité invalide']);
    exit;
}

require_once '../db.php';

$prenom = trim($_POST['prenom'] ?? '');
$nom = trim($_POST['nom'] ?? '');

if (empty($prenom) || empty($nom)) {
    echo json_encode(['success' => false, 'message' => 'Prénom et nom requis']);
    exit;
}

try {
    // Obtenir l'IP de l'utilisateur
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    
    $stmt = $pdo->prepare("INSERT INTO secret_finders (prenom, nom, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$prenom, $nom, $ip]);
    
    echo json_encode([
        'success' => true,
        'message' => '🎉 Bravo ! Votre exploit a été enregistré.'
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur secret_finder: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'enregistrement'
    ]);
}
