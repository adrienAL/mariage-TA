<?php
/**
 * Headers de sécurité HTTP
 * Protection contre XSS, clickjacking, MIME sniffing, etc.
 */

// Empêche l'inclusion dans une iframe (protection clickjacking)
header("X-Frame-Options: SAMEORIGIN");

// Empêche le navigateur de deviner le type MIME
header("X-Content-Type-Options: nosniff");

// Protection XSS intégrée au navigateur
header("X-XSS-Protection: 1; mode=block");

// Contrôle du referrer
header("Referrer-Policy: strict-origin-when-cross-origin");

// Permissions des API du navigateur
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

// Content Security Policy (CSP)
// Note: 'unsafe-inline' est nécessaire pour le code inline actuel
// À améliorer en déplaçant tout le JS/CSS vers des fichiers externes
$csp = implode('; ', [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline'",
    "style-src 'self' 'unsafe-inline'",
    "img-src 'self' data:",
    "font-src 'self'",
    "connect-src 'self'",
    "media-src 'self'",
    "object-src 'none'",
    "frame-ancestors 'self'",
    "base-uri 'self'",
    "form-action 'self'"
]);
header("Content-Security-Policy: $csp");

// Strict Transport Security (HSTS) - Force HTTPS pendant 1 an
// ATTENTION: À activer UNIQUEMENT quand HTTPS fonctionne bien !
// Décommenter la ligne ci-dessous une fois HTTPS en place
// header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
