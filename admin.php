<?php
// Sécurité : Forcer HTTPS (sauf localhost)
require_once 'force_https.php';

// Sécurité : Configuration sécurisée des sessions
require_once 'session_config.php';

// Sécurité : Headers HTTP sécurisés
require_once 'security_headers.php';

require_once 'env_loader.php';
require_once 'admin_protection.php';

// Protection admin
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
            <title>Admin - Connexion</title>
            <link rel="stylesheet" href="assets/style.css">
            <style>
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
                    padding: 2rem; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .login { 
                    max-width: 400px; 
                    width: 100%;
                    background: white; 
                    padding: 2rem; 
                    border-radius: 1rem;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                }
                h2 { 
                    color: #667eea; 
                    margin-bottom: 1.5rem;
                    text-align: center;
                }
                input { 
                    width: 100%; 
                    padding: 0.75rem; 
                    margin: 0.5rem 0;
                    border: 1px solid #ddd;
                    border-radius: 0.5rem;
                    font-size: 1rem;
                }
                button[type="submit"] { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    color: white; 
                    border: none; 
                    padding: 0.75rem 1.5rem; 
                    cursor: pointer;
                    width: 100%;
                    border-radius: 0.5rem;
                    font-size: 1rem;
                    font-weight: 600;
                    margin-top: 1rem;
                    transition: transform 0.2s;
                }
                button[type="submit"]:hover {
                    transform: translateY(-2px);
                }
                .error {
                    color: #ff4444;
                    margin-top: 1rem;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="login">
                <h2>🔐 Administration</h2>
                <form method="post">
                    <div class="password-wrapper">
                        <input type="password" id="admin-password-input" name="admin_password" placeholder="Mot de passe admin" required>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('admin-password-input')">
                          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <button type="submit">Se connecter</button>
                    <?php if (isset($_POST['admin_password'])): ?>
                        <p class="error">Mot de passe incorrect</p>
                    <?php endif; ?>
                </form>
            </div>
            <script src="assets/app.js"></script>
        </body>
        </html>
        <?php
        exit;
    }
}

// Gérer la déconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged']);
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Administration - Mariage Tiphaine & Adrien</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .container {
      max-width: 800px;
      width: 100%;
      background: white;
      border-radius: 1rem;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      padding: 3rem;
    }
    
    h1 {
      color: #667eea;
      margin-bottom: 0.5rem;
      font-size: 2rem;
    }
    
    .subtitle {
      color: #666;
      margin-bottom: 2rem;
      font-size: 1rem;
    }
    
    .admin-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }
    
    .admin-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 0.75rem;
      padding: 2rem;
      text-decoration: none;
      color: white;
      transition: transform 0.2s, box-shadow 0.2s;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    
    .admin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }
    
    .admin-card .icon {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }
    
    .admin-card h2 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
    }
    
    .admin-card p {
      font-size: 0.9rem;
      opacity: 0.9;
      line-height: 1.4;
    }
    
    .logout-btn {
      display: inline-block;
      margin-top: 2rem;
      padding: 0.75rem 1.5rem;
      background: #ff4444;
      color: white;
      text-decoration: none;
      border-radius: 0.5rem;
      font-weight: 600;
      transition: background 0.2s;
    }
    
    .logout-btn:hover {
      background: #cc0000;
    }
    
    @media (max-width: 768px) {
      body {
        padding: 1rem;
      }
      
      .container {
        padding: 1.5rem;
      }
      
      h1 {
        font-size: 1.5rem;
      }
      
      .admin-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🔐 Administration</h1>
    <p class="subtitle">Mariage Tiphaine & Adrien - 24 octobre 2026</p>
    
    <div class="admin-grid">
      <a href="view_rsvp.php" class="admin-card">
        <div class="icon">📋</div>
        <h2>Réponses RSVP</h2>
        <p>Consulter les confirmations de présence et les réservations Shaduns</p>
      </a>
      
      <a href="view_secret_finders.php" class="admin-card">
        <div class="icon">🎯</div>
        <h2>Trouveurs de secrets</h2>
        <p>Liste des invités qui ont découvert le code secret</p>
      </a>
      
      <a href="view_login_logs.php" class="admin-card">
        <div class="icon">📊</div>
        <h2>Logs de connexion</h2>
        <p>Historique des tentatives de connexion au site</p>
      </a>
      
      <a href="index.php" class="admin-card">
        <div class="icon">🏠</div>
        <h2>Retour au site</h2>
        <p>Retourner sur le site principal du mariage</p>
      </a>
    </div>
    
    <a href="?logout=1" class="logout-btn">⎋ Déconnexion admin</a>
  </div>
</body>
</html>
