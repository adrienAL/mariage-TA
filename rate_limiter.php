<?php
/**
 * Gestionnaire de rate limiting pour éviter les attaques par force brute
 */

class RateLimiter {
    private $pdo;
    private $tableName = 'rate_limit';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }
    
    /**
     * Crée la table si elle n'existe pas
     */
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableName} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            endpoint VARCHAR(100) NOT NULL,
            attempts INT DEFAULT 1,
            last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            locked_until DATETIME NULL,
            INDEX idx_ip_endpoint (ip_address, endpoint),
            INDEX idx_locked (locked_until)
        )";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erreur création table rate_limit: " . $e->getMessage());
        }
    }
    
    /**
     * Vérifie si l'IP peut faire une requête
     * @param string $endpoint Identifiant de l'endpoint (ex: 'login', 'rsvp')
     * @param int $maxAttempts Nombre max de tentatives
     * @param int $windowSeconds Fenêtre de temps en secondes
     * @param int $lockoutSeconds Durée de blocage en secondes
     * @return bool True si autorisé, False si bloqué
     */
    public function checkLimit($endpoint, $maxAttempts = 5, $windowSeconds = 60, $lockoutSeconds = 300) {
        $ip = $this->getClientIp();
        
        // Nettoyer les anciennes entrées
        $this->cleanup($windowSeconds);
        
        // Vérifier si l'IP est verrouillée
        $stmt = $this->pdo->prepare(
            "SELECT locked_until FROM {$this->tableName} 
             WHERE ip_address = ? AND endpoint = ? AND locked_until > NOW()"
        );
        $stmt->execute([$ip, $endpoint]);
        
        if ($stmt->fetch()) {
            return false; // IP verrouillée
        }
        
        // Compter les tentatives récentes
        $stmt = $this->pdo->prepare(
            "SELECT attempts FROM {$this->tableName} 
             WHERE ip_address = ? AND endpoint = ? 
             AND last_attempt > DATE_SUB(NOW(), INTERVAL ? SECOND)"
        );
        $stmt->execute([$ip, $endpoint, $windowSeconds]);
        $row = $stmt->fetch();
        
        if ($row && $row['attempts'] >= $maxAttempts) {
            // Verrouiller l'IP
            $this->lockIp($ip, $endpoint, $lockoutSeconds);
            return false;
        }
        
        return true;
    }
    
    /**
     * Enregistre une tentative
     */
    public function recordAttempt($endpoint) {
        $ip = $this->getClientIp();
        
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->tableName} (ip_address, endpoint, attempts, last_attempt) 
             VALUES (?, ?, 1, NOW()) 
             ON DUPLICATE KEY UPDATE 
                attempts = attempts + 1,
                last_attempt = NOW()"
        );
        
        try {
            $stmt->execute([$ip, $endpoint]);
        } catch (PDOException $e) {
            error_log("Erreur enregistrement tentative: " . $e->getMessage());
        }
    }
    
    /**
     * Réinitialise les tentatives pour une IP (après succès)
     */
    public function reset($endpoint) {
        $ip = $this->getClientIp();
        
        $stmt = $this->pdo->prepare(
            "DELETE FROM {$this->tableName} WHERE ip_address = ? AND endpoint = ?"
        );
        
        try {
            $stmt->execute([$ip, $endpoint]);
        } catch (PDOException $e) {
            error_log("Erreur reset rate limit: " . $e->getMessage());
        }
    }
    
    /**
     * Verrouille une IP
     */
    private function lockIp($ip, $endpoint, $lockoutSeconds) {
        $stmt = $this->pdo->prepare(
            "UPDATE {$this->tableName} 
             SET locked_until = DATE_ADD(NOW(), INTERVAL ? SECOND) 
             WHERE ip_address = ? AND endpoint = ?"
        );
        
        try {
            $stmt->execute([$lockoutSeconds, $ip, $endpoint]);
        } catch (PDOException $e) {
            error_log("Erreur verrouillage IP: " . $e->getMessage());
        }
    }
    
    /**
     * Nettoie les anciennes entrées
     */
    private function cleanup($windowSeconds) {
        $stmt = $this->pdo->prepare(
            "DELETE FROM {$this->tableName} 
             WHERE last_attempt < DATE_SUB(NOW(), INTERVAL ? SECOND) 
             AND (locked_until IS NULL OR locked_until < NOW())"
        );
        
        try {
            $stmt->execute([$windowSeconds * 2]); // Garder 2x la fenêtre
        } catch (PDOException $e) {
            error_log("Erreur nettoyage rate limit: " . $e->getMessage());
        }
    }
    
    /**
     * Récupère l'IP du client
     */
    private function getClientIp() {
        // Vérifier les en-têtes proxy
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }
    
    /**
     * Obtient le temps restant de blocage en secondes
     */
    public function getRemainingLockTime($endpoint) {
        $ip = $this->getClientIp();
        
        $stmt = $this->pdo->prepare(
            "SELECT TIMESTAMPDIFF(SECOND, NOW(), locked_until) as remaining 
             FROM {$this->tableName} 
             WHERE ip_address = ? AND endpoint = ? AND locked_until > NOW()"
        );
        $stmt->execute([$ip, $endpoint]);
        $row = $stmt->fetch();
        
        return $row ? (int)$row['remaining'] : 0;
    }
}
