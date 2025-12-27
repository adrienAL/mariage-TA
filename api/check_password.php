<?php
header('Content-Type: application/json');
require_once '../security_headers.php';
require_once '../session_config.php';

require_once '../db.php';
require_once '../env_loader.php';
require_once '../rate_limiter.php';

// Initialiser le rate limiter
$rateLimiter = new RateLimiter($pdo);

// Vérifier le rate limit (5 tentatives par minute, blocage de 5 minutes)
if (!$rateLimiter->checkLimit('login', 5, 60, 300)) {
    $remaining = $rateLimiter->getRemainingLockTime('login');
    $minutes = ceil($remaining / 60);
    echo json_encode([
        'success' => false, 
        'message' => "Trop de tentatives. Réessayez dans {$minutes} minute(s)."
    ]);
    http_response_code(429); // Too Many Requests
    exit;
}

// Mots de passe depuis les variables d'environnement
$PASSWORD = EnvLoader::get('PASSWORD_SHADUNS');
$PASSWORD_NO_SHADUNS = EnvLoader::get('PASSWORD_NO_SHADUNS');
$SECRET_PASSWORD = EnvLoader::get('PASSWORD_SECRET');

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
$canSeeShaduns = true;

if ($input === $PASSWORD) {
    $_SESSION['logged_in'] = true;
    $_SESSION['secret_access'] = false;
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['user_nom'] = $nom;
    $_SESSION['can_see_shaduns'] = true;
    $isValidPassword = true;
    $passwordType = 'shaduns';
    $canSeeShaduns = true;
} elseif ($input === $PASSWORD_NO_SHADUNS) {
    $_SESSION['logged_in'] = true;
    $_SESSION['secret_access'] = false;
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['user_nom'] = $nom;
    $_SESSION['can_see_shaduns'] = false;
    $isValidPassword = true;
    $passwordType = 'normal';
    $canSeeShaduns = false;
} elseif ($input === $SECRET_PASSWORD) {
    $_SESSION['logged_in'] = true;
    $_SESSION['secret_access'] = true;
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['user_nom'] = $nom;
    $_SESSION['can_see_shaduns'] = true;
    $isValidPassword = true;
    $passwordType = 'secret';
    $canSeeShaduns = true;
}

if ($isValidPassword) {
    // Réinitialiser le compteur de tentatives
    $rateLimiter->reset('login');
    
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
    // Enregistrer la tentative échouée
    $rateLimiter->recordAttempt('login');
    
    echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
}


