<?php
session_start();

require_once 'env_loader.php';

// Protection simple - à améliorer si besoin
$ADMIN_PASSWORD = EnvLoader::get('ADMIN_PASSWORD');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    if (isset($_POST['admin_password']) && $_POST['admin_password'] === $ADMIN_PASSWORD) {
        $_SESSION['admin_logged'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Admin - Secret Finders</title>
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
