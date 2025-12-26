<?php
/**
 * Protection renforcée pour les pages d'administration
 * IP Whitelisting + Rate Limiting spécifique admin
 */

class AdminProtection {
    /**
     * Vérifie si l'IP est autorisée à accéder à l'admin
     * @param array $allowedIps Liste des IPs autorisées (vide = toutes autorisées)
     * @return bool
     */
    public static function checkIpWhitelist($allowedIps = []) {
        // Si aucune IP spécifiée, on autorise tout (désactivé)
        if (empty($allowedIps)) {
            return true;
        }
        
        $clientIp = self::getClientIp();
        
        // Autoriser localhost en développement
        $localhostIps = ['127.0.0.1', '::1'];
        if (in_array($clientIp, $localhostIps)) {
            return true;
        }
        
        // Vérifier si l'IP est dans la whitelist
        return in_array($clientIp, $allowedIps);
    }
    
    /**
     * Bloque l'accès si l'IP n'est pas autorisée
     * @param array $allowedIps Liste des IPs autorisées
     */
    public static function enforceIpWhitelist($allowedIps = []) {
        if (!self::checkIpWhitelist($allowedIps)) {
            http_response_code(403);
            die('Accès refusé. Votre adresse IP n\'est pas autorisée.');
        }
    }
    
    /**
     * Récupère l'IP réelle du client (gère les proxys)
     * @return string
     */
    private static function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Prendre la première IP si plusieurs
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }
    
    /**
     * Ajoute un délai progressif en cas d'échec de connexion admin
     * @param int $failedAttempts Nombre de tentatives échouées
     */
    public static function addLoginDelay($failedAttempts) {
        if ($failedAttempts > 0) {
            // Délai exponentiel : 2^tentatives secondes (max 32s)
            $delay = min(pow(2, $failedAttempts), 32);
            sleep($delay);
        }
    }
    
    /**
     * Enregistre une tentative de connexion admin en session
     */
    public static function recordFailedAttempt() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['admin_failed_attempts'])) {
            $_SESSION['admin_failed_attempts'] = 0;
        }
        
        $_SESSION['admin_failed_attempts']++;
        $_SESSION['admin_last_attempt'] = time();
    }
    
    /**
     * Réinitialise le compteur de tentatives échouées
     */
    public static function resetFailedAttempts() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['admin_failed_attempts'] = 0;
    }
    
    /**
     * Récupère le nombre de tentatives échouées
     * @return int
     */
    public static function getFailedAttempts() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['admin_failed_attempts'] ?? 0;
    }
}

// Configuration : IPs autorisées pour l'admin
// Laisser vide [] pour autoriser toutes les IPs
// Remplir avec vos IPs : ['123.45.67.89', '98.76.54.32']
$ADMIN_ALLOWED_IPS = EnvLoader::get('ADMIN_ALLOWED_IPS', '');
if (!empty($ADMIN_ALLOWED_IPS)) {
    $ADMIN_ALLOWED_IPS = explode(',', $ADMIN_ALLOWED_IPS);
    $ADMIN_ALLOWED_IPS = array_map('trim', $ADMIN_ALLOWED_IPS);
} else {
    $ADMIN_ALLOWED_IPS = []; // Vide = toutes IPs autorisées
}
