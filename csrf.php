<?php
/**
 * Gestionnaire de tokens CSRF (Cross-Site Request Forgery)
 * Protège les formulaires contre les attaques CSRF
 */

class CSRF {
    private static $sessionKey = 'csrf_tokens';
    private static $tokenLength = 32;
    
    /**
     * Génère un nouveau token CSRF
     * @param string $formName Nom du formulaire (ex: 'login', 'rsvp')
     * @return string Le token généré
     */
    public static function generateToken($formName = 'default') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Créer un token aléatoire sécurisé
        $token = bin2hex(random_bytes(self::$tokenLength));
        
        // Stocker le token en session
        if (!isset($_SESSION[self::$sessionKey])) {
            $_SESSION[self::$sessionKey] = [];
        }
        
        $_SESSION[self::$sessionKey][$formName] = [
            'token' => $token,
            'time' => time()
        ];
        
        // Nettoyer les vieux tokens (> 1 heure)
        self::cleanOldTokens();
        
        return $token;
    }
    
    /**
     * Vérifie si un token est valide
     * @param string $token Token à vérifier
     * @param string $formName Nom du formulaire
     * @param bool $deleteAfterUse Supprimer le token après validation (défaut: true)
     * @return bool True si valide, False sinon
     */
    public static function validateToken($token, $formName = 'default', $deleteAfterUse = true) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier que le token existe en session
        if (!isset($_SESSION[self::$sessionKey][$formName])) {
            return false;
        }
        
        $storedData = $_SESSION[self::$sessionKey][$formName];
        
        // Vérifier l'expiration (1 heure)
        if ((time() - $storedData['time']) > 3600) {
            unset($_SESSION[self::$sessionKey][$formName]);
            return false;
        }
        
        // Comparer les tokens de manière sécurisée
        $isValid = hash_equals($storedData['token'], $token);
        
        // Supprimer le token après utilisation (protection contre replay)
        if ($isValid && $deleteAfterUse) {
            unset($_SESSION[self::$sessionKey][$formName]);
        }
        
        return $isValid;
    }
    
    /**
     * Génère un champ input hidden pour un formulaire HTML
     * @param string $formName Nom du formulaire
     * @return string HTML du champ hidden
     */
    public static function getInputField($formName = 'default') {
        $token = self::generateToken($formName);
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Récupère le token pour l'envoyer en JSON
     * @param string $formName Nom du formulaire
     * @return string Le token
     */
    public static function getToken($formName = 'default') {
        return self::generateToken($formName);
    }
    
    /**
     * Nettoie les tokens expirés
     */
    private static function cleanOldTokens() {
        if (!isset($_SESSION[self::$sessionKey])) {
            return;
        }
        
        $now = time();
        foreach ($_SESSION[self::$sessionKey] as $formName => $data) {
            if (($now - $data['time']) > 3600) {
                unset($_SESSION[self::$sessionKey][$formName]);
            }
        }
    }
    
    /**
     * Supprime tous les tokens
     */
    public static function clearAll() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION[self::$sessionKey]);
    }
    
    /**
     * Vérifie le token depuis une requête POST ou JSON
     * @param string $formName Nom du formulaire
     * @return bool True si valide, False sinon
     */
    public static function validateRequest($formName = 'default') {
        // Vérifier POST classique
        if (isset($_POST['csrf_token'])) {
            return self::validateToken($_POST['csrf_token'], $formName);
        }
        
        // Vérifier JSON
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $data = json_decode($json, true);
            if (isset($data['csrf_token'])) {
                return self::validateToken($data['csrf_token'], $formName);
            }
        }
        
        // Vérifier header personnalisé
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            return self::validateToken($_SERVER['HTTP_X_CSRF_TOKEN'], $formName);
        }
        
        return false;
    }
}
