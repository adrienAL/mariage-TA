<?php
// Sécurité : Forcer HTTPS (sauf localhost)
require_once 'force_https.php';

// Sécurité : Configuration sécurisée des sessions
require_once 'session_config.php';

// Sécurité : Headers HTTP sécurisés
require_once 'security_headers.php';

require_once 'env_loader.php';
require_once 'admin_protection.php';

// Protection IP (optionnel - activer si nécessaire)
// AdminProtection::enforceIpWhitelist($ADMIN_ALLOWED_IPS);

// Protection simple - à améliorer si besoin
$ADMIN_PASSWORD = EnvLoader::get('ADMIN_PASSWORD');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    if (isset($_POST['admin_password']) && $_POST['admin_password'] === $ADMIN_PASSWORD) {
        AdminProtection::resetFailedAttempts();
        $_SESSION['admin_logged'] = true;
    } else {
        // Ajouter un délai si tentatives précédentes échouées
        if (isset($_POST['admin_password'])) {
            AdminProtection::recordFailedAttempt();
            $failedAttempts = AdminProtection::getFailedAttempts();
            AdminProtection::addLoginDelay($failedAttempts - 1);
        }
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Admin - Secret Finders</title>
            <link rel="stylesheet" href="assets/style.css">
            <style>
                body { font-family: Arial, sans-serif; padding: 2rem; background: #f5f5f5; }
                .login { max-width: 400px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; }
                input { width: 100%; padding: 0.5rem; margin: 0.5rem 0; }
                button[type="submit"] { background: #007bff; color: white; border: none; padding: 0.5rem 1rem; cursor: pointer; width: 100%; }
            </style>
        </head>
        <body>
            <div class="login">
                <h1>Connexion Admin</h1>
                <form method="POST">
                    <div class="password-wrapper">
                        <input type="password" id="admin-pwd-secret" name="admin_password" placeholder="Mot de passe admin">
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('admin-pwd-secret')">
                          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <button type="submit">Se connecter</button>
                </form>
            </div>
            <script src="assets/app.js"></script>
        </body>
        </html>
        <?php
        exit;
    }
}

require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM secret_finders ORDER BY date_found DESC");
    $finders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ceux qui ont trouvé le code secret</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .count {
            font-size: 1.2rem;
            color: #007bff;
            margin: 1rem 0;
        }
        .logout {
            margin-top: 1rem;
        }
        .logout a {
            color: #dc3545;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏆 Les trouveurs du code secret</h1>
        <p class="count">Total : <?php echo count($finders); ?> personne(s)</p>
        
        <?php if (empty($finders)): ?>
            <p>Personne n'a encore trouvé le code secret.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Date de découverte</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($finders as $index => $finder): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($finder['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($finder['nom']); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($finder['date_found'])); ?></td>
                        <td><?php echo htmlspecialchars($finder['ip_address'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="logout">
            <a href="admin.php" style="margin-right: 1rem; color: #007bff; text-decoration: none;">← Retour à l'admin</a>
            <a href="?logout=1">Se déconnecter</a>
        </div>
    </div>
</body>
</html>
<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: view_secret_finders.php');
    exit;
}
?>
