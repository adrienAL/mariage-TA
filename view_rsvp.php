<?php
// Sécurité : Forcer HTTPS (sauf localhost)
require_once 'force_https.php';

// Sécurité : Configuration sécurisée des sessions
require_once 'session_config.php';

// Sécurité : Headers HTTP sécurisés
require_once 'security_headers.php';

require_once 'env_loader.php';
require_once 'admin_protection.php';
require_once 'db.php';

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
            <title>Admin - RSVP</title>
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

// Récupération des paramètres
$filter = $_GET['filter'] ?? 'all';
$sort = $_GET['sort'] ?? 'date_desc';

// Construction de la requête
$whereClause = '';
if ($filter === 'oui') {
    $whereClause = "WHERE presence = 'oui'";
} elseif ($filter === 'non') {
    $whereClause = "WHERE presence = 'non'";
}

// Ordre de tri
$orderClause = match($sort) {
    'date_asc' => 'ORDER BY created_at ASC',
    'date_desc' => 'ORDER BY created_at DESC',
    'nom_asc' => 'ORDER BY nom ASC, prenom ASC',
    'nom_desc' => 'ORDER BY nom DESC, prenom DESC',
    default => 'ORDER BY created_at DESC'
};

// Récupération des données
$stmt = $pdo->query("SELECT * FROM rsvps $whereClause $orderClause");
$rsvps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques RSVP
$statsStmt = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN presence = 'oui' THEN 1 ELSE 0 END) as total_oui,
        SUM(CASE WHEN presence = 'non' THEN 1 ELSE 0 END) as total_non,
        SUM(CASE WHEN presence = 'oui' THEN nb_personnes ELSE 0 END) as total_personnes,
        SUM(CASE WHEN presence = 'oui' AND brunch = 'oui' THEN nb_personnes ELSE 0 END) as total_brunch
    FROM rsvps
");
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Statistiques Shaduns
$shadunsStatsStmt = $pdo->query("
    SELECT 
        COUNT(DISTINCT CONCAT(prenom_contact, nom_contact)) as total_reservations,
        COUNT(*) as total_personnes
    FROM shaduns_resa_personnes
");
$shadunsStats = $shadunsStatsStmt->fetch(PDO::FETCH_ASSOC);

// Liste des réservations Shaduns groupées par contact
$shadunsStmt = $pdo->query("
    SELECT 
        prenom_contact,
        nom_contact,
        GROUP_CONCAT(personne_nom ORDER BY id SEPARATOR ', ') as personnes,
        COUNT(*) as nb_personnes,
        MIN(created_at) as created_at
    FROM shaduns_resa_personnes
    GROUP BY prenom_contact, nom_contact
    ORDER BY created_at DESC
");
$shadunsResa = $shadunsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - RSVP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 2rem;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #007bff;
        }
        h1 { color: #333; font-size: 1.8rem; }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-card.danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }
        .stat-card h3 {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
        }
        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .filters select {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            cursor: pointer;
        }
        .filters label {
            font-weight: 500;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        thead {
            background: #007bff;
            color: white;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        tbody tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .badge.oui {
            background: #d4edda;
            color: #155724;
        }
        .badge.non {
            background: #f8d7da;
            color: #721c24;
        }
        .message-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .message-cell:hover {
            white-space: normal;
            overflow: visible;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #999;
        }
        .section-title {
            font-size: 1.5rem;
            margin: 3rem 0 1.5rem 0;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }
        .section-title:first-of-type {
            margin-top: 0;
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        @media (max-width: 768px) {
            body { padding: 1rem; }
            .container { padding: 1rem; }
            table { font-size: 0.85rem; }
            th, td { padding: 0.5rem; }
            .stats { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📋 Admin - Mariage Tiphaine & Adrien</h1>
            <a href="?logout=1" class="logout-btn">Déconnexion</a>
        </header>

        <!-- Section RSVP -->
        <h2 class="section-title">📨 Réponses RSVP</h2>

        <div class="stats">
            <div class="stat-card">
                <h3>Total réponses</h3>
                <div class="value"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card success">
                <h3>Présents confirmés</h3>
                <div class="value"><?php echo $stats['total_oui']; ?></div>
            </div>
            <div class="stat-card danger">
                <h3>Absents</h3>
                <div class="value"><?php echo $stats['total_non']; ?></div>
            </div>
            <div class="stat-card success">
                <h3>Total personnes</h3>
                <div class="value"><?php echo $stats['total_personnes']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Présents au brunch</h3>
                <div class="value"><?php echo $stats['total_brunch']; ?></div>
            </div>
        </div>

        <div class="filters">
            <div>
                <label for="filter">Filtrer :</label>
                <select id="filter" onchange="updateFilters()">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Tous</option>
                    <option value="oui" <?php echo $filter === 'oui' ? 'selected' : ''; ?>>Présents uniquement</option>
                    <option value="non" <?php echo $filter === 'non' ? 'selected' : ''; ?>>Absents uniquement</option>
                </select>
            </div>
            
            <div>
                <label for="sort">Trier par :</label>
                <select id="sort" onchange="updateFilters()">
                    <option value="date_desc" <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>>Date (plus récent)</option>
                    <option value="date_asc" <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>>Date (plus ancien)</option>
                    <option value="nom_asc" <?php echo $sort === 'nom_asc' ? 'selected' : ''; ?>>Nom (A-Z)</option>
                    <option value="nom_desc" <?php echo $sort === 'nom_desc' ? 'selected' : ''; ?>>Nom (Z-A)</option>
                </select>
            </div>
        </div>

        <?php if (count($rsvps) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Présence</th>
                    <th>Nb personnes</th>
                    <th>Brunch</th>
                    <th>Message</th>
                    <th>Date réponse</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rsvps as $rsvp): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rsvp['id']); ?></td>
                    <td><?php echo htmlspecialchars($rsvp['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($rsvp['nom']); ?></td>
                    <td>
                        <span class="badge <?php echo $rsvp['presence']; ?>">
                            <?php echo $rsvp['presence'] === 'oui' ? '✓ Oui' : '✗ Non'; ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($rsvp['nb_personnes']); ?></td>
                    <td><?php echo $rsvp['brunch'] === 'oui' ? '✓' : '✗'; ?></td>
                    <td class="message-cell" title="<?php echo htmlspecialchars($rsvp['message']); ?>">
                        <?php echo htmlspecialchars($rsvp['message']); ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($rsvp['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <p>Aucune réponse pour le moment.</p>
        </div>
        <?php endif; ?>

        <!-- Section Shaduns -->
        <h2 class="section-title">🏠 Réservations Dortoir Shaduns</h2>

        <div class="stats">
            <div class="stat-card warning">
                <h3>Réservations</h3>
                <div class="value"><?php echo $shadunsStats['total_reservations']; ?></div>
            </div>
            <div class="stat-card warning">
                <h3>Places réservées</h3>
                <div class="value"><?php echo $shadunsStats['total_personnes']; ?> / 40</div>
            </div>
            <div class="stat-card <?php echo $shadunsStats['total_personnes'] >= 40 ? 'danger' : 'success'; ?>">
                <h3>Places restantes</h3>
                <div class="value"><?php echo max(0, 40 - $shadunsStats['total_personnes']); ?></div>
            </div>
        </div>

        <?php if (count($shadunsResa) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Contact</th>
                    <th>Nb personnes</th>
                    <th>Liste des personnes</th>
                    <th>Date réservation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shadunsResa as $resa): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($resa['prenom_contact'] . ' ' . $resa['nom_contact']); ?></strong></td>
                    <td><?php echo htmlspecialchars($resa['nb_personnes']); ?></td>
                    <td class="message-cell" title="<?php echo htmlspecialchars($resa['personnes']); ?>">
                        <?php echo htmlspecialchars($resa['personnes']); ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($resa['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <p>Aucune réservation pour le moment.</p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function updateFilters() {
            const filter = document.getElementById('filter').value;
            const sort = document.getElementById('sort').value;
            window.location.href = `?filter=${filter}&sort=${sort}`;
        }
    </script>
</body>
</html>

<?php
// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: view_rsvp.php');
    exit;
}
?>
