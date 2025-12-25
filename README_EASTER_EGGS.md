# 🎉 Easter Eggs - Système de Code Secret

## Fonctionnalités implémentées

### 1. Easter Egg Adrien avec détection de touches ✨
- Quand on clique sur "Adrien" dans les noms, sa photo apparaît
- **Si une touche du clavier est pressée** pendant le clic, la photo reste affichée **1 seconde de plus** (3 secondes au lieu de 2)

### 2. Code secret 4815162342 🔐
- L'utilisateur peut taper la séquence `4815162342` n'importe où sur le site
- Un popup apparaît avec le message : **"c&7Xo#32-v"**
- Ce message est le mot de passe secret pour accéder à la zone cachée

### 3. Formulaire secret 🏆
- En entrant le mot de passe `c&7Xo#32-v` sur la page de connexion, l'utilisateur accède à un formulaire spécial
- Il peut y enregistrer son nom et prénom
- Les données sont sauvegardées dans la table `secret_finders`

### 4. Page admin pour voir les trouveurs 👀
- Accessible via `view_secret_finders.php`
- Mot de passe admin : `admin2026TA`
- Affiche la liste de tous ceux qui ont trouvé le code

## Installation

### 1. Créer la table dans la base de données
Exécutez le fichier SQL :
```bash
mysql -u votre_utilisateur -p votre_base < create_secret_table.sql
```

Ou exécutez directement cette requête :
```sql
CREATE TABLE IF NOT EXISTS secret_finders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    date_found TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Fichiers modifiés
- ✅ `index.php` - Ajout du formulaire secret
- ✅ `assets/app.js` - Détection des touches et code secret
- ✅ `api/check_password.php` - Gestion du mot de passe secret
- ✅ `api/secret_finder.php` - Enregistrement des trouveurs (nouveau)
- ✅ `view_secret_finders.php` - Page admin (nouveau)
- ✅ `create_secret_table.sql` - Script SQL (nouveau)

## Comment ça marche

### Scénario utilisateur
1. L'utilisateur navigue sur le site
2. Il tape `4815162342` sur son clavier
3. Un popup apparaît avec le code : `c&7Xo#32-v`
4. Il se déconnecte (ou ouvre un autre navigateur)
5. Il entre le code `c&7Xo#32-v` comme mot de passe
6. Il arrive sur le formulaire secret
7. Il enregistre son nom
8. Il est redirigé vers la page principale

### Pour voir qui a trouvé
1. Allez sur `view_secret_finders.php`
2. Connectez-vous avec le mot de passe admin : `admin2026TA`
3. Vous verrez la liste complète avec dates et IPs

## Sécurité

⚠️ **Important** : Le mot de passe admin est en dur dans le code. Pour une meilleure sécurité en production :
- Changez le mot de passe admin dans `view_secret_finders.php`
- Utilisez un hash pour le mot de passe
- Protégez le fichier avec `.htaccess` si possible

## Tests

### Tester l'easter egg Adrien
1. Cliquez sur "Adrien" → la photo apparaît 2 secondes
2. Maintenez une touche enfoncée et cliquez sur "Adrien" → la photo reste 3 secondes

### Tester le code secret
1. Tapez `4815162342` (les chiffres peuvent être dispersés dans le temps)
2. Le popup avec le code doit apparaître

### Tester le formulaire secret
1. Sur la page de connexion, entrez `c&7Xo#32-v`
2. Remplissez le formulaire
3. Vérifiez dans `view_secret_finders.php` que l'entrée a été enregistrée

## Personnalisation

### Changer le code secret
Dans `assets/app.js`, ligne ~720 :
```javascript
const SECRET_CODE = '4815162342'; // Votre nouveau code
const SECRET_MESSAGE = 'c&7Xo#32-v'; // Votre nouveau message
```

### Changer le mot de passe secret
Dans `api/check_password.php` :
```php
$SECRET_PASSWORD = "c&7Xo#32-v"; // Votre nouveau mot de passe
```

### Changer le mot de passe admin
Dans `view_secret_finders.php` :
```php
$ADMIN_PASSWORD = "admin2026TA"; // Votre nouveau mot de passe admin
```

---

Bon amusement ! 🎊
