<?php
/**
 * Gestionnaire de configuration sécurisé
 * Charge les variables depuis le fichier .env
 */

class EnvLoader {
    private static $config = [];
    private static $loaded = false;

    /**
     * Charge le fichier .env
     */
    public static function load($envPath = null) {
        if (self::$loaded) {
            return;
        }

        if ($envPath === null) {
            $envPath = __DIR__ . '/.env';
        }

        if (!file_exists($envPath)) {
            // En production, on peut utiliser de vraies variables d'environnement
            // Pour l'instant, on lève une erreur
            error_log("ERREUR CRITIQUE: Fichier .env introuvable à " . $envPath);
            die("Configuration manquante. Contactez l'administrateur.");
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parser les lignes KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Supprimer les guillemets si présents
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                self::$config[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Récupère une valeur de configuration
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        return self::$config[$key] ?? $default;
    }

    /**
     * Vérifie si une clé existe
     */
    public static function has($key) {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$config[$key]);
    }
}

// Charger automatiquement la config
EnvLoader::load();
