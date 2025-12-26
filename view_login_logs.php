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
            <title>Admin - Logs de connexion</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 2rem; background: #f5f5f5; }
                .login { max-width: 400px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; }
                input { width: 100%; padding: 0.5rem; margin: 0.5rem 0; }
                button { background: #007bff; color: white; border: none; padding: 0.5rem 1rem; cursor: pointer; }
            </style>
        </head>
        <body>
            <div class="login">
                <h1>Connexion Admin</h1>
                <form method="POST">
                    <input type="password" name="admin_password" placeholder="Mot de passe admin">
                    <button type="submit">Se connecter</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

require_once 'db.php';

// Filtres
$filterType = $_GET['type'] ?? 'all';
$filterDate = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';

try {
    $sql = "SELECT * FROM login_logs WHERE 1=1";
    $params = [];
    
    if ($filterType !== 'all') {
        $sql .= " AND password_type = ?";
        $params[] = $filterType;
    }
    
    if ($filterDate) {
        $sql .= " AND DATE(login_date) = ?";
        $params[] = $filterDate;
    }
    
    if ($search) {
        $sql .= " AND (prenom LIKE ? OR nom LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY login_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistiques
    $statsStmt = $pdo->query("
        SELECT 
            COUNT(*) as total,
            COUNT(DISTINCT CONCAT(nom, prenom)) as unique_users,
            SUM(CASE WHEN password_type = 'normal' THEN 1 ELSE 0 END) as normal_logins,
            SUM(CASE WHEN password_type = 'secret' THEN 1 ELSE 0 END) as secret_logins
        FROM login_logs
    ");
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logs de connexion - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
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
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            color: #666;
            font-size: 0.9rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .filters {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin: 1.5rem 0;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .filters input, .filters select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filters button {
            background: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
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
            font-weight: 600;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-normal {
            background: #d1ecf1;
            color: #0c5460;
        }
        .badge-secret {
            background: #f8d7da;
            color: #721c24;
        }
        .logout {
            margin-top: 1rem;
        }
        .logout a {
            color: #dc3545;
            text-decoration: none;
        }
        .export-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Logs de connexion</h1>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total connexions</h3>
                <div class="value"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Utilisateurs uniques</h3>
                <div class="value"><?php echo $stats['unique_users']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Connexions normales</h3>
                <div class="value"><?php echo $stats['normal_logins']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Connexions secrètes 🔐</h3>
                <div class="value"><?php echo $stats['secret_logins']; ?></div>
            </div>
        </div>
        
        <form method="GET" class="filters">
            <input type="text" name="search" placeholder="Rechercher nom/prénom" value="<?php echo htmlspecialchars($search); ?>">
            <select name="type">
                <option value="all" <?php echo $filterType === 'all' ? 'selected' : ''; ?>>Tous les types</option>
                <option value="normal" <?php echo $filterType === 'normal' ? 'selected' : ''; ?>>Normal</option>
                <option value="secret" <?php echo $filterType === 'secret' ? 'selected' : ''; ?>>Secret</option>
            </select>
            <input type="date" name="date" value="<?php echo htmlspecialchars($filterDate); ?>">
            <button type="submit">Filtrer</button>
            <a href="view_login_logs.php" class="export-btn">Réinitialiser</a>
        </form>
        
        <?php if (empty($logs)): ?>
            <p>Aucune connexion enregistrée.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Date & Heure</th>
                        <th>IP</th>
                        <th>Navigateur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $index => $log): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($log['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($log['nom']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $log['password_type']; ?>">
                                <?php echo $log['password_type'] === 'secret' ? '🔐 Secret' : '🔑 Normal'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['login_date'])); ?></td>
                        <td><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></td>
                        <td style="font-size: 0.85rem; color: #666;">
                            <?php 
                                $ua = $log['user_agent'] ?? '';
                                echo strlen($ua) > 50 ? htmlspecialchars(substr($ua, 0, 50)) . '...' : htmlspecialchars($ua); 
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="logout">
            <a href="?logout=1">Se déconnecter</a>
        </div>
    </div>
</body>
</html>
<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: view_login_logs.php');
    exit;
}
?>
