<?php
/**
 * Configuration sécurisée des sessions PHP
 * À inclure AVANT session_start() dans tous les fichiers
 */

// Configuration des cookies de session sécurisés
ini_set('session.cookie_httponly', '1');      // Empêche JavaScript d'accéder aux cookies de session
ini_set('session.cookie_secure', '1');        // Cookies uniquement via HTTPS (désactiver en dev local HTTP)
ini_set('session.cookie_samesite', 'Strict'); // Protection CSRF supplémentaire
ini_set('session.use_strict_mode', '1');      // Refuse les IDs de session non générés par le serveur
ini_set('session.use_only_cookies', '1');     // N'utilise que les cookies (pas URL)

// Régénération de l'ID de session pour éviter la fixation
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Régénérer l'ID toutes les 30 minutes
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
    
    // Vérifier l'IP et User-Agent pour détecter les vols de session (optionnel, peut causer des problèmes avec IP dynamique)
    /*
    $current_fingerprint = md5($_SERVER['HTTP_USER_AGENT'] ?? '');
    if (!isset($_SESSION['fingerprint'])) {
        $_SESSION['fingerprint'] = $current_fingerprint;
    } elseif ($_SESSION['fingerprint'] !== $current_fingerprint) {
        // Possible vol de session
        session_unset();
        session_destroy();
        session_start();
    }
    */
}
