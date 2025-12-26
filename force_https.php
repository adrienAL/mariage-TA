<?php
/**
 * Force la redirection vers HTTPS
 * À inclure au tout début des fichiers PHP publics
 */

// Vérifier si la connexion est en HTTPS
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

// Rediriger vers HTTPS si ce n'est pas le cas
if (!$isHttps) {
    // En développement local, vous pouvez commenter cette redirection
    // ou vérifier l'environnement
    
    // Ignorer localhost en développement
    $isLocalhost = (
        $_SERVER['HTTP_HOST'] === 'localhost' ||
        strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === 0 ||
        strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0
    );
    
    if (!$isLocalhost) {
        $redirect_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect_url);
        exit;
    }
}
