<?php
// Sécurité : Forcer HTTPS (sauf localhost)
require_once 'force_https.php';

// Sécurité : Configuration sécurisée des sessions
require_once 'session_config.php';

// Sécurité : Headers HTTP sécurisés
require_once 'security_headers.php';

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
  session_destroy();
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<!--                      SITE MARIAGE TIPHAINE & ADRIEN 2026 
xxxxxxxxxxxxXXXXXXXxXXXXXXXXxXxxxXXXXXXXXXXXXXXXXXXXXXXXXXxxXXxxxxxXxxxXxxxxxxxxxxxxxxxxxxxxxxxxxxxx
XXXXXXXXXXXXXxXXXXXXXXxXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXxxxxxxxxxxxxXxxxXXxXXXXXXXXXXXXXXX$$$$$
XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX$$$XXXXXXXXXXXXXXXX$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
XXXXXXXXXXXXXXXXXXXXXXXX$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$X$$$$$$$$$$$$$$$$$$$$$$$
XX$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$XXX$$$$$$$$$$$$$$$$$$XXXXXXXXXX$$$$$$$$$$$XXXX$$$$$$$$$$$
xXXXXX$$$$$$$$$$$$$$$$$$$$$$$$$$XXX$$$$$XXXxxxxXXX$$$$XXXXXXXXXXXXXXXX$$$$$$$$$$$$$XXXXXXXXXX$$$$$$$
xXXXxxXXXXXXXXXXXXXXXXXX$$$$XxxxxxxxXXXXXxxxx+x++xxxX$$XXXXXXXXXX$$$$$$$$$$$$$$X$XxXXX$$$$$$X$$$$$$$
xXXXxxxxXXXXXXXXXXXXXXX$$$$x++++++xxxxxxx+++++++;++;+xX$$XXXXXX$$$$$$$$$$$$$$$$$$$$$XXX$$$$$XX$$$$$$
xXXXXxxxXXXXXXXXXXXXXXX$$Xx+++++++++++++++;;;;;;;::;;;+X$$XXX$$$$$$$&$$$$$$$$$$$X$$XXXXX$$$$$$$$$$$$
xXXXXXXxXXXXXXXXXXXXXX$$$Xx++xxx+++;;;;;:;;;;::::::::;+xX$$X$$$$$$&&&$$$$$$$$$XXXXXXxxxxX$$$$$$$$$$$
XXXXXXXXXXXXXXXXXXXXX$$XXx+xxxxxx+;;;;:::::::::::::::;;xXX$$&&&&&&$$$$$$$$$$XXxx+++;;;;;;+X$$$$$$$$$
XXXXXXXXXXXXXXXXXXXX$$XXXxxxxxxxx++;;;;;:::::::::...:;+xX$$&&&&&$$$$$$$$XXxxx+++;;+;;;;;;;;x$$$$$$$$
XxXXXXXXX$$$$$$$$$$$$$XXxxxxxxxx+++;;;;;::::::::....:;+xX$$&&&&&&$&$$XXxxxx+++++++;;;;:::::;x$$$$XX$
XxXXXXXX$$$$$$$$$$$$$$XXxxxxxxxx++;;;;;;;::::::.....:;+X$$$&&&&&&&$XXxxxxx+++++;+;;;;:::::::;XX$$XXX
XxXXXXXX$$$$$$$$$$$$$$Xxxxxxxxxx+;;;;;;;:::::::.....:;+X$$&&&&&&$XXXxxxxx++++++;+;;;;:::.:::;x$$XXXX
XXXXXXXX$$$$$$$$Xxx$$$Xxxxxxxxx++;;;;::::::::::.....:;+X$$&&&&&$XXXxxxxxxx++++;;;;;;;::::.:::+X$XXxx
XXXXXXXXX$$$$$$$XXxX$$xxxxXXXXxx++;;;:::::::::......:;+X$&&&&&$Xxxxxxxxxxx+++++;;;;;::::...::+x$XXxx
XXXXXXXXX$$$$$$$XxxX$XxxXXXXXX$XXX+;;;;;::::::.....::;+$&&&&$XXxxxxxxxxxxx++++;;;;;;::::...::;xXXXx+
XXxXXXXXX$$$$$$$XX$$Xxxxxxx++xxXXXXXx++;;:::::::::.::;x$&&&&$XXxxxxxxxxxx+++++;;;;;;:::::::::;xXXXxx
XXxXXXXXX$$$$$$$X$$$Xx++xXXXXXXXXXXxXxx+;;+Xx+xxx+;;;;x$&&&$XXXXX$$$$$Xx+++++;;;;;;+xxXXx+;;;;xXXXxx
XXXXXXXXX$$$$$$$$$$$Xxx++++x+;;;+XXXXX+::;+xxxXXXxxxxxX$&&&$XXXXxxxxXXXXx++xx;;;;++++++;;::..;xXXxXx
XXXXXXXXX$$$$$$$$$$Xxxx;+;;;;;+xxXXXXx;:;+xxXXX++++xxx$&&&&$XXxxXXXXXXXXXXxxx;:::;+xX$$x+;+;::+xXxXx
XXXXXXXXX$$$$$$$$$$Xxxx++;;;;;+xXXXXX+;::x;++++++x+;+XX&&&&$XXXX$$X$$XX$$XXXx+:.:+XXXXXX++++xxxxxXXX
XXXXXXXXXX$$$$$$$$$x+xx+++x+;++xXXXXx+;::+;:;;;::;;+xX++&&$$XXX$XXXXxxX$$$XXX+:::;xXxx+;;;;::::+xxXX
XXXXXXXXXX$&$$Xx$$Xx++++;;;+++xXXXXXx+:::;;:;;:::;;+xX$&&&$XXXXXXXXXXXXXXXXXX+:::::;+++++;;:...:xX$$
XXXXXXXXXX$&$++X$$$Xx+++;;++xxXXxxxx+;::::;;;;;::::+xX&&&&&XxxxxxxxxXXXXXXXXX+;::..:;;++;;::....+$$X
$$XXXXXXXX+;;;x$$$$XXx+++xXXXXXX+x++;::.:::;;;;::.:;x$&&&&&XxxxxxxxxXXXXXXXXXx;;:::x++++;::....:;$$$
$$$$XXXXx;;;;;X$$$$X$XXXX$$$$$$$$$Xx;;:+;::;;;;:..:;x$&&&&&XxxxxxxxxxXXXXXXXx+;:::::xxx++;:.....;$$$
$$$$XXX+;;;;;+X$$$$$$$XXX$$$X$XXXx++x++++;:;x+;;::;+X&&&&&&Xxxxxxx++xXXXxxxxx;;:::::+xXXx+;::.:.;$$$
$X+;;;;;;;;;;+xX$$$$$$$XXXX$$$Xxxx+Xxx+x+++++xx+;;+X$&&&&&&$Xxxx+++xXXXXXXXxx++;;::::;+xXx+;::.:+$$$
+;;;;;;+++;;;++x$$$$$$X$XXXXXXXXx+++++++xxxxxxXx+xX$&&&&&&&$Xxxx++xxXXXXXXxx++++;;::::;+xxx+::.:x$$$
;;:;;:;;+++++++xX$$$$$$$XXxxxxx++;;;;;;;;;xXXXxxXX$$&&&&&&&&Xxxx++xXX$$XxXXXxx+;::..X$x+Xx+;:::;X$$$
::::::::;++++++++$$$$$$$Xxxxx+++++;;;:::::+XXXXXX$$&&&&&&&&&&Xxxx++XXX$$xx++:::...:+;::;x+;::;;$$$$X
..:..::::;;+++++;;X$$$$$$$$XXxxxxxx+;;;;;;+XXXX$$$$&&$$&&&&&&&XxxxxxxxXXXXXXx;xx++::::::+;:;++$$X$$$
..::...::::++++++;x$$$$$$$XXXXXxxxXXx++++xX$$$X$$$&&$$&&&&&&&&&XxxxxxxxxxXXXxxx++;::::::;+++X&&XX$$X
:.::::..::::;++++;+X$$$$$$XXXXXXXXxx++xxXX$$X$$$$&$$$&&&&&&&&&&&$XXxxxxxxxx+++;;;::::::+++X$&&$X$$$x
::.:::..:::::++++:;X$$$$$$$XXXXXXXxxxxXX$X$$$$$$&$$$&&&&&&&&&&&&&&$XXxxxxxxx+;;;::::::++x$$&&$x$$$Xx
:::.:::..::::;+++::+X$$$X$$X$$X$XXXXXXX$$$$$$$&&$$$&&&&&&&&&&&&&&&&&$Xxxxxxx++;:::::;+x$$$&$$x$$$XXx
:::..;:..::::;;++..:xXXXXXXX$$$$X$XXX$$$$$$&&$$&$&&&&&&&&&$$&&&&&&&&&&$Xxxx++++;;:;+x$$$$$$XX$$$XXXx
::::::::..:::;;;:...:XXXXXxxxxxXX$X$$$$$$$$$&$$$$&&&&&&&$$$&&&&&&&&&&&&&$Xxxxx++xX$$$$$$$$X$$XX$XXXX
::::.::::.:;:;;;:....+xxxxxxxXXX$$Xxx$$$X$XX$$$$X$&&&&&&$$$&&$&&&&$$Xx+X$$$$$$$$$$$$$$XX$$$XX$$XXXXX
::::::.::::;;;;;:....:+++xXX$$$$$XxxX$XXXx+xXxxXxX$&&&&&$$X$$$&&&$$X++::;X$$$$$$$$$$$$X$$XX$$$X$$XXX
:::::::::::;;;;;:.....;+x$$$$$&$xxx+;;xXx++++;;;;;+$&&&&&$$$$&$&&$$$x+;:::+X$$$$$$$$$$$XXX$$$X$$$$$$
:::.:::::::;+;;::.....;xX$&$&&Xxxxx+;;:;+++x+;;;:::x&&&&&&$$$$$&&$$$$X+:::;;x$$$$$$$$$$X$$$$X$$$$$$&
::::::::::;;+;::......:X$$&&&Xxx++;;;;::.;;++++;::..X&&&&&&&&$&&&$$$$$Xx;::::;X$$$$$$$$$&$$$$&$$&&&&
::::.:::::;;;;;:......;$$$&&$x+;;;;::::::.:;;;;;;::.:X&$&&&&$$&&&&$$$$$Xx+;;:.;x$$$$$$&&$$$$$$$$&&&&
::::.:::::;;;;::......+$$&&$x++;;;;;:;:.:...;;;;;;:..+$$&&&$$&&&&&&$$$$$X+;;;:::+$$$$$&$$$$$$$$$&&&$
::::.::.::;+;;:.......+$$&&X++;;;;;;;::::....:;+++:::;$$$$$&&$&&&&&&$$$$XX+;;;;;+X$&$$$$&&$$$$$$&&$$
::::::...:;;+::..:....+$&&$+;;;;;;;;;;:::.....:;++;;;+X$$$$$$$&&$&&&&$$$$$$x+++++$&$$$&&&$$$$$X$$&$$
::::::...:;;;::.::....X$&&x;;:;;;;;;;;;:::....:;+++;;++X$$XX++x$&&&&&$$$$$$$Xx+x+$&$$$&$$$$$XXXX$&&$
:::::::..:;;;::.::...:$&&X;;::::::::;;;;;::....::;+x+;;:....:;+x$&&&&$$$$&$$$XxxxX&&$$$$$$$$$XxxX$&&
:::::.::::;;;::.::...;x&$+;;:::::;;::;;;;;::....:;++;;::::::;++X&&&&$$$$&&&&$$Xxxx$&$$$$$$$XXxxxxX$&
:;::...:.:;;::.:::...;x$x+;::::;::;:::::;;;:....:;;+;;;;:;;;+xX$$&&&$$$&&&&&$$$$$XX$$$&&$$$$Xx+++x$&
::;:::.:..:;:::.:...:+xx+;:::::::::;::::::;;;::::;;+;;;;;;;+++xXXXX&&$X$&&&&&&$$$XXX$&&$$$$$x++;++x$
.::;::::....::::::::+++x+;:::;;;:;:::::::::;;;:::;;+++;;;;;;+++x+XX$$$$$$$$&&&&$$$XXXX&$$$$xx+;;+++$
:..:;::::...::::::;;++++;::::;;;:::::::::::::;;;;;;+++;;;;;;;++++xxXXxX$Xxxx$&&&&$$XXxX&$$$X++;;;++X
:...:;;:::..:.::;;;++++;;::;;;;;;;;:::::::::;;;;;;;;++;;;;;;;+;++++++xx+++x+xxXX$$$$$Xx$&$$$x+;;;;+x
....::;:::::.::;;;+++++;:::;:;;;;;;;;;;;;;;;;;;;++;;++;;;;;;;;;;;+;;;;;;;;;++++xxxxXXXxX$$$$X+;;;;;x
:....::::::::::;++++++;;:::;;;;;;;;;;;;;;;;;;;;;;++;+++;;;;;;;;;;;;;;;;;;;;;+;+++++xXXXXxX$$$x;;;;;+
:::::::::::::;;+++++++;:;::;;;;;;;;;;;;;;;;;;;;;;;+++++;;;;;;;;;;;+;;;;;;;;;;;;;+++xxXX+;;+$XXx+;;;+
:::::::::;;;;;;+++++++;:::;;;;;;;;;;;;;;;;;;;;;;;;+++++;;;;;;;;;;;;;;;;;;;;;;;;;;+++XX+;;;;;xX+++;;+
;;;:::::;;+++++++++++;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;++++;;;;;;;;;;;;;;;;;;;;;;;;;;++x+x+;;;;;;;+++;;;
;;;;;;;;;;++++++++xx;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;++x+;;;;;;;;;;;;;;;;;;;;;;;;;;;+++x+;;;;;;;;;;;;;
;;;;++++;;++++++++xx;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;+x+;;;;;;;;;;;;;;;;;;;;;;;;;;;;;+x+;;;;;;;;;;;;;
;;;;++++++++++++++xx;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;+xx;;;;;;;;;;;;;;;;;;;;;;;:::;;++++;;;::;;;;:;;;
+;++++++++++++x+++x+;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;+xx;;;;;;;;;;;;;;;;;;;;;;::;;;;;++;;;;;::::;::::
++++++++++++xxxxxxx;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;xx;;;;;;;;;;;;;;;;;;;;;:;;;;;;;+;;;;;::::::::::
+++++++++++++xXxxxx;;;;;;;;;;;;;;;;+;++;;;;;;;;;;;;;;+x+;;;;;;;;;;;;;;;;;::;;;;;;:;;;;;;;;::::::::::
;;;++++++++++xxXxx+;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;xx+;;;;;::::;;;::::;;;;;;;;;:::;;;;;;::::::::::
;;;;;;+++++++xxXXx;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;+x+;;;;;;:;;::::;;;;;;;;;;;;:::;;;;;;::::::::::
;;;;;;;;;+++xxxx+;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;:::;x+;;;;;:::::::;;;;;;;;;;;;;:::;;;;;;::::::::::
;;;;;;;;;;;+++x;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;::::;++;;;:;;;:::;;;;;;;;;;;;;;::::;;:::::::::::::
++++;;;;;;;;+;;;;;;;;;;+++;+;;;;;;;;;;;;;;;;;;;;;;;:::;++;;;;;;::::;;;;;;;;;;;;;;:::::::::::::::::::
Lost : désactiver le bunker sur ma gueule de con 
-->
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mariage Tiphaine & Adrien – 24 octobre 2026</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="assets/logo.png">
  <link rel="stylesheet" href="assets/style.css?v=<?php echo filemtime('assets/style.css'); ?>">
</head>
<body>
<?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
  <?php
    // Récupération des paramètres de requête
    $queryPrenom = isset($_GET['prenom']) ? htmlspecialchars($_GET['prenom']) : '';
    $queryNom = isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : '';
    $queryPassword = isset($_GET['password']) ? htmlspecialchars($_GET['password']) : '';
    $autoLogin = !empty($queryPrenom) && !empty($queryNom) && !empty($queryPassword);
  ?>
  <!-- ÉCRAN MOT DE PASSE -->
  <div id="password-screen">
    <div class="pwd-card">
      <h1>Mariage de Tiphaine & Adrien</h1>
      <p>Entrez vos informations pour accéder au site ✨</p>
      
      <div class="form-row">
        <input type="text" id="pwd-prenom" placeholder="Prénom" value="<?php echo $queryPrenom; ?>" required>
      </div>
      <div class="form-row">
        <input type="text" id="pwd-nom" placeholder="Nom" value="<?php echo $queryNom; ?>" required>
      </div>
      <div class="form-row">
        <input type="password" id="pwd-input" placeholder="Mot de passe" value="<?php echo $queryPassword; ?>" required>
      </div>
      
      <button id="pwd-btn">Entrer</button>
      <p id="pwd-error" class="error"></p>
    </div>
  </div>
  <?php if ($autoLogin): ?>
  <script>
    // Connexion automatique si les paramètres sont présents
    window.addEventListener('DOMContentLoaded', () => {
      const pwdBtn = document.getElementById('pwd-btn');
      if (pwdBtn) {
        setTimeout(() => {
          pwdBtn.click();
        }, 100);
      }
    });
  </script>
  <?php endif; ?>

<?php elseif (isset($_SESSION['secret_access']) && $_SESSION['secret_access'] === true): ?>

  <!-- FORMULAIRE SECRET POUR CEUX QUI ONT TROUVÉ LE CODE -->
  <style>
    body.loading { overflow: hidden; }
    #secret-form-container { opacity: 0; transition: opacity .6s ease; }
    body.loaded #secret-form-container { opacity: 1; }
  </style>

  <script>
    document.body.classList.add('loading');
    window.addEventListener('load', () => {
      document.body.classList.remove('loading');
      document.body.classList.add('loaded');
    });
  </script>

  <div id="secret-form-container">
    <header class="topbar">
      <div class="brand">🔓 Zone Secrète</div>
    </header>

    <main style="padding: 4rem 2rem; max-width: 600px; margin: 0 auto; min-height: 100vh;">
      <h1 style="text-align: center; margin-bottom: 2rem;">🎉 Félicitations !</h1>
      <p style="text-align: center; margin-bottom: 2rem;">
        Vous avez trouvé le code secret ! Pour immortaliser votre exploit, 
        laissez-nous votre nom ci-dessous.
      </p>

      <form id="secret-finder-form" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="form-row">
          <label>Prénom</label>
          <input type="text" name="prenom" value="<?php echo isset($_SESSION['user_prenom']) ? htmlspecialchars($_SESSION['user_prenom']) : ''; ?>" required>
        </div>
        <div class="form-row">
          <label>Nom</label>
          <input type="text" name="nom" value="<?php echo isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : ''; ?>" required>
        </div>
        <button type="submit">Enregistrer mon exploit 🏆</button>
        <p id="secret-status"></p>
      </form>

      <div style="text-align: center; margin-top: 2rem;">
        <a href="?logout=1" style="color: #666; text-decoration: underline;">Retour au site principal</a>
      </div>
    </main>
  </div>

<?php else: ?>

  <!-- Effet de fondu progressif du site au chargement -->
  <style>
    body.loading { overflow: hidden; }
    #app { opacity: 0; transition: opacity .6s ease; }
    body.loaded #app { opacity: 1; }
  </style>

  <script>
    document.body.classList.add('loading');
    window.addEventListener('load', () => {
      document.body.classList.remove('loading');
      document.body.classList.add('loaded');
    });
  </script>

  <!-- APPLICATION SPA -->
  <div id="app">
    <!-- Musique persistante -->
    <!--<audio id="bg-music" src="assets/music.mp3" autoplay loop></audio>-->

    <!-- BARRE DE NAVIGATION FIXE -->
    <header class="topbar">
        <div class="brand">Tiphaine & Adrien</div>

    
      <!-- Bouton burger pour mobile -->
      <button class="burger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
        <span></span>
        <span></span>
        <span></span>
      </button>
    
      <nav class="tabs" id="main-nav">
        <button class="tab-btn" data-tab="home">Accueil</button>
        <button class="tab-btn" data-tab="rsvp">Je confirme ma présence</button>
        <button class="tab-btn" data-tab="logements">Logements</button>
        <button class="tab-btn" data-tab="contact">Nous contacter</button>
      </nav>
    </header>


    <!-- CONTENU PRINCIPAL -->
    <main id="content">

      <!-- SECTION ACCUEIL -->
      <section id="home" class="page active">
        <div class="hero-fullscreen">
          <div class="carousel" aria-hidden="false">
            <div class="carousel-track">
              <?php
                // Dossiers d'images (attention à l'orthographe exacte dans assets)
                $desktopDir = 'assets/carrousel-horizontal';
                $mobileDir = 'assets/carroussel-vertical';

                $filterImages = function($files) {
                  return array_values(array_filter($files, function($f) {
                    return preg_match('/\.(jpe?g|png|gif|webp)$/i', $f);
                  }));
                };

                $desktopFiles = is_dir($desktopDir) ? $filterImages(scandir($desktopDir)) : [];
                $mobileFiles = is_dir($mobileDir) ? $filterImages(scandir($mobileDir)) : [];

                // Si pas d'images desktop, fallback sur quelques images existantes
                if (empty($desktopFiles)) {
                  $desktopFiles = ['photo_home.jpg','montfriol-domain-global.jpg','montfriol-allee-nuit.jpg'];
                }

                foreach ($desktopFiles as $i => $dfile) {
                  $dpath = $desktopDir . '/' . $dfile;
                  $mpath = isset($mobileFiles[$i]) ? ($mobileDir . '/' . $mobileFiles[$i]) : null;
                  // Si le fichier desktop n'existe pas (fallback to root assets)
                  if (!file_exists($dpath)) {
                    $dpath = 'assets/' . $dfile;
                  }
                  if ($mpath && !file_exists($mpath)) {
                    $mpath = null;
                  }
              ?>
              <div class="slide">
                <picture>
                  <?php if ($mpath): ?>
                    <source media="(max-width:768px)" srcset="<?php echo htmlspecialchars($mpath); ?>">
                  <?php endif; ?>
                  <img src="<?php echo htmlspecialchars($dpath); ?>" alt="">
                </picture>
              </div>
              <?php } /* endforeach desktopFiles */ ?>
            </div>
            <button class="carousel-btn prev" aria-label="Précédent">‹</button>
            <button class="carousel-btn next" aria-label="Suivant">›</button>
            <div class="carousel-dots" aria-hidden="true"></div>
          </div>
          <div class="overlay">
            <?php if (isset($_SESSION['user_prenom']) && !empty($_SESSION['user_prenom'])): ?>
            <p class="hero-greeting">
              Bonjour <?php echo htmlspecialchars($_SESSION['user_prenom']); ?>, nous sommes ravis de t'inviter à notre mariage
            </p>
            <?php endif; ?>
            <h1 class="hero-names">
              <span class="hn-first easter-egg-trigger" data-egg="tiphaine">Tiphaine</span>
              <span class="hn-et">&</span>
              <span class="hn-last easter-egg-trigger" data-egg="adrien">Adrien</span>
            </h1>
            <div class="date-container">
              <div class="wedding-date">24 octobre 2026</div>
              <div class="wedding-location">Domaine de Montfriol, Chambost-Allières</div>
            </div>

            <div id="countdown" class="countdown">
              <div><span id="cd-days">0</span><small>jours</small></div>
              <div><span id="cd-hours">0</span><small>heures</small></div>
              <div><span id="cd-mins">0</span><small>minutes</small></div>
              <div><span id="cd-secs">0</span><small>secondes</small></div>
            </div>

            <button id="hero-scroll" class="scroll-down" aria-label="Descendre">↓</button>
          </div>
        </div>
        
        <!-- INTRO SOUS LE HERO -->
        <section class="intro">
          <p>
            Nous avons le plaisir de vous inviter à célébrer notre mariage au 
            <br /><strong>Domaine de Montfriol</strong><br /> 
            Vous trouverez ici toutes les informations pratiques ainsi<br />
            que le formulaire pour confirmer votre présence.<br />
            Nous avons hâte de partager ce moment si spécial avec vous.
          </p>
        </section>

        <!-- Déroulé -->
        <div id="deroule" class="scroll-area">
          <h2>Le déroulé de la journée</h2>
          <ul class="timeline">
            <li>
              <h3>Programme de la journée</h3>
              <p>
                La journée débutera à <strong>16h00</strong> avec notre <strong>cérémonie laïque</strong> au 
                <strong>Domaine de Montfriol</strong>. Nous vous invitons à arriver quelques minutes en avance 
                pour vous installer tranquillement.
              </p>
              <p>
                Après la cérémonie, nous partagerons un <strong>vin d'honneur</strong> convivial dans la cours du domaine, 
                suivi d'un <strong>dîner</strong> dans la salle de réception. 
                </p>
                <p>
                Le lendemain se poursuivra avec 
                <strong>galettes saucisses et jeux</strong> pour finir en douceur.
              </p>
            </li>
          </ul>

          <h2>Infos sur le domaine</h2>
          <p>
            Le mariage aura lieu au <strong>Domaine de Montfriol</strong>,
            à Chambost-Allières, dans le Beaujolais. Le domaine dispose d’un grand
            espace extérieur pour le vin d’honneur, d’une belle salle de réception
            et de plusieurs coins chaleureux pour profiter de la soirée. Pensez à
            prévoir un petit gilet pour la fin de journée 🫶
          </p>
          
          <div class="domain-gallery">
            <img src="assets/montfriol-allee-nuit.jpg" alt="">
            <img src="assets/montfriol-back.jpg" alt="">
            <!--<img src="assets/montfriol-bar-a-biere.jpg" alt="">
            <img src="assets/montfriol-domain-global.jpg" alt="">-->
            <img src="assets/montfriol-cave.jpg" alt="">
            <img src="assets/montfriol-escalier.jpg" alt="">
            <img src="assets/montfriol-terrasse-avant.jpg" alt="">
            <img src="assets/montfriol-terrasse-jour.jpg" alt="">
          </div>

        </div>
        <section id="egg-zone" class="egg-zone" aria-hidden="true">
          <div class="egg-inner">
            <h3>Ok voici un peux plus de mignonnerie</h3>
            <img src="assets/easter-egg/egg-chats.jpg" alt="Easter egg chats" loading="lazy">
          </div>
        </section>
      </section>




      <!-- SECTION LOGEMENTS -->
      <section id="logements" class="page">
        <h2>Logements</h2>
        
        <div class="lodging-group">
          <h3>Les témoins, demoiselles d'honneur et maître de cérémonie</h3>
          <p>
            Le gîte du domaine sera réservé pour eux et leurs +1.
          </p>
        </div>

        <?php if (!isset($_SESSION['can_see_shaduns']) || $_SESSION['can_see_shaduns'] === true): ?>
        <div class="lodging-group">
            <h3>Les Shaduns</h3>
            <p>
                  Les dortoirs du gîte du Domaine de la Vielle, juste en face du domaine,
              sont réservés pour vous : 
              <strong>
                1000&nbsp;€ à diviser par le nombre de personnes pour le week-end, 
                venez nombreux<sup>*</sup>&nbsp;😜
              </strong>.
            </p>
            <p>
            Merci d’amener votre duvet.
            </p>
            <p class="note-dortoir">
              <sup>*</sup> Dans la limite des stocks disponibles (max. 40&nbsp;personnes).
            </p>
            <button type="button" class="btn-reserver open-shaduns">
              Réserver ma place au dortoir
            </button>
        </div>
        <?php endif; ?>

        <section class="lodging-wrap">
          <h2>Logements à proximité</h2>

          <div class="lodging-group">
            <h3>La Grange Fleurie – Chamelet</h3>
            <p><strong>Capacité :</strong> jusqu’à 16 personnes</p>
            <p><strong>Adresse :</strong> 1092 rue de la Concorde, 69620 Chamelet</p>
            <p><strong>Réservation :</strong> via le site internet ou Airbnb</p>
            <p><strong>Contact :</strong></p>
            <ul>
              <li>Delphine : <a href="tel:+33680120323">06 80 12 03 23</a></li>
              <li>Damien : <a href="tel:+33661717089">06 61 71 70 89</a></li>
              <li>Email : <a href="mailto:lagrangefleurie1092@gmail.com">lagrangefleurie1092@gmail.com</a></li>
            </ul>

            <div class="lodging-actions">
              <a class="btn-reserver" href="https://www.lagrangefleurie-chamelet.fr" target="_blank" rel="noopener">
                Voir le site
              </a>
            </div>
          </div>

          <div class="lodging-group">
            <h3>La Muzetière – Chamelet</h3>
            <p><strong>Distance :</strong> environ 6 km du domaine</p>
            <p><strong>Hébergements :</strong> 2 gîtes et 2 cabanes</p>
            <ul>
              <li>Gîte La Muzetière (5 personnes) : 350 € les 2 nuits</li>
              <li>Gîte Gisèle et Fred (6 personnes) : 400 € les 2 nuits</li>
              <li>Cabanes (2 à 4 personnes) :
                <ul>
                  <li>170 € / nuit pour 2</li>
                  <li>220 € / nuit jusqu’à 4 personnes (avec petit-déjeuner)</li>
                  <li>300 € pour 2 nuits à 2 personnes</li>
                </ul>
              </li>
            </ul>
            <p><strong>Réservation :</strong> contacter Alban</p>
            <p><strong>Contact :</strong> <a href="tel:+33757540078">07 57 54 00 78</a></p>

            <div class="lodging-actions">
              <a class="btn-reserver" href="https://www.lamuzetière.fr" target="_blank" rel="noopener">
                Voir le site
              </a>
            </div>
          </div>

          <div class="lodging-group">
            <h3>Évasion Entre Plaines et Forêts</h3>
            <p><strong>Distance :</strong> environ 5 km du domaine</p>
            <p><strong>Capacité :</strong> jusqu’à 6 personnes</p>
            <p><strong>Configuration :</strong></p>
            <ul>
              <li>1 chambre avec lit double</li>
              <li>1 chambre avec 2 lits simples</li>
              <li>1 canapé convertible dans le salon</li>
            </ul>
            <p><strong>Tarif mariage :</strong> 35 € / personne / nuit (210 € la nuit pour 6 personnes)</p>
            <p><strong>Réservation :</strong> contacter William Chermette</p>
            <p><strong>Contact :</strong> <a href="tel:+33664102592">06 64 10 25 92</a></p>

          </div>

          <div class="lodging-group">
            <h3>Domaine Martine Mousset</h3>
            <p><strong>Capacité :</strong> 8 personnes</p>
            <p><strong>Configuration :</strong> 4 chambres – logement privatisé</p>
            <p><strong>Tarif :</strong> 288 € / nuit</p>
            <p>Tarif incluant le petit-déjeuner, le linge de toilette et les taxes.</p>
            <p><strong>Réservation :</strong> par téléphone</p>
            <p><strong>Contact :</strong> <a href="tel:+33637669695">06 37 66 96 95</a></p>

          </div>

          <div class="lodging-group">
            <h3>Dormir à l’École</h3>
            <p><strong>Capacité :</strong> 10 personnes</p>
            <p><strong>Configuration :</strong></p>
            <ul>
              <li>5 chambres individuelles</li>
              <li>3 chambres avec lit double (140)</li>
              <li>2 chambres avec lit simple (90)</li>
            </ul>
            <p><strong>Équipements :</strong> petit coin pour déjeuner</p>
            <p><strong>Réservation :</strong> contacter Sandrine Chassignol</p>
            <p><strong>Contact :</strong> <a href="tel:+33783981929">07 83 98 19 29</a></p>
          </div>

          <div class="lodging-group">
            <h3>Domaine Dumas Annie – Ternand</h3>
            <p><strong>Type :</strong> chambres d’hôtes</p>
            <p><strong>Réservation :</strong> contacter Annie Dumas</p>
            <p><strong>Contact :</strong> <a href="tel:+33683326921">06 83 32 69 21</a></p>
          </div>

        </section>

        </section>
      
        <!-- SECTION SHADUNS (page interne) -->
        <section id="shaduns" class="page">
          <h2>Réservation du dortoir – Les Shaduns</h2>
          <p>
            Les dortoirs du gîte du Domaine de la Vielle (en face du domaine) sont
            réservés pour les Shaduns : 40&nbsp;€/personne pour le week-end.
            Merci d’amener votre duvet.
          </p>
        
          <form id="shaduns-form">
            <div class="form-row">
              <label>Prénom</label>
              <input type="text" name="prenom_contact" value="<?php echo isset($_SESSION['user_prenom']) ? htmlspecialchars($_SESSION['user_prenom']) : ''; ?>" required>
            </div>
            <div class="form-row">
              <label>Nom</label>
              <input type="text" name="nom_contact" value="<?php echo isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : ''; ?>" required>
            </div>
        
            <div class="form-row">
              <label>Nombre de personnes (toi inclus)</label>
              <input type="number" name="nb_personnes" id="shaduns-nb" min="1" value="1" required>
            </div>
        
            <div id="shaduns-extra-guests"></div>
        
            <button type="submit">Envoyer ma réponse</button>
            <p id="shaduns-status"></p>
          </form>
        </section>


      <!-- SECTION RSVP -->
      <section id="rsvp" class="page">
        <h2>Je confirme ma présence</h2>
        <p class="alert">Ce mariage est sans enfant 💛 Merci de votre compréhension.</p>

        <form id="rsvp-form">
          <div class="form-row">
            <label>Prénom</label>
            <input type="text" name="prenom" value="<?php echo isset($_SESSION['user_prenom']) ? htmlspecialchars($_SESSION['user_prenom']) : ''; ?>" required>
          </div>
          <div class="form-row">
            <label>Nom</label>
            <input type="text" name="nom" value="<?php echo isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : ''; ?>" required>
          </div>
        
          <div class="form-row">
            <label>Présence</label>
            <select name="presence" id="rsvp-presence" required>
              <option value="oui">Oui, je serai là 🎉</option>
              <option value="non">Non, j'ai mieux à faire (obligé de répondre ça si je ne viens pas)</option>
            </select>
          </div>
        
          <!-- Tout ce bloc sera caché si présence = non -->
          <div id="rsvp-extra-fields">
            <div class="form-row">
              <label>Nombre de personnes</label>
              <input type="number" name="nb_personnes" min="1" max="5" value="1">
            </div>
        
            <!-- Champs prénoms supplémentaires -->
            <div id="extra-guests"></div>
        
            <div class="form-row">
              <label>Présent pour les galettes saucisses ?</label>
              <select name="brunch">
                <option value="oui">Ouiiiii</option>
                <option value="non">Ho non je peux pas j'ai poney !</option>
              </select>
            </div>
        
            <div class="form-row">
              <label>Petit message de ce que tu veux ...</label>
              <textarea name="message" rows="4" placeholder="Des mots doux ou un regime sans gluten, végétarien, allergies..."></textarea>
            </div>
          </div>
        
          <button type="submit">Envoyer ma réponse</button>
          <p id="rsvp-status" hidden="true"></p>
        </form>
      </section>
    </main>
  </div>
<?php endif; ?>

<!-- Lightbox pour les photos du domaine -->
<div id="lightbox" class="lightbox-overlay" aria-hidden="true">
  <button class="lightbox-close" aria-label="Fermer la photo">&times;</button>
  <img id="lightbox-img" src="" alt="">
</div>

<!-- Popup Rickroll interne -->
<div id="rickroll-overlay" class="popup-overlay">
  <div class="popup-box" style="max-width:420px; width:90%; text-align:center;">
    <button class="popup-close" aria-label="Fermer">×</button>

    <p style="margin-bottom:1rem; font-size:1rem;">
      Tu voulais vraiment nous contacter ?
    </p>

    <video id="rickroll-video" controls style="width:100%; border-radius:0.6rem;">
      <source src="assets/RickRoll.mp4" type="video/mp4">
      Ton navigateur est NULLLLL !
    </video>
  </div>
</div>


<!-- Popup globale -->
<div id="global-popup" class="popup-overlay" aria-hidden="true">
  <div class="popup-box">
    <button class="popup-close" aria-label="Fermer la notification">&times;</button>
    <p id="popup-message">Message</p>
  </div>
</div>

<!-- Popup Easter Egg -->
<div id="easter-egg-popup" class="easter-egg-overlay">
  <img id="easter-egg-img" src="" alt="Easter Egg">
</div>

<script src="assets/csrf-helper.js?v=<?php echo filemtime('assets/csrf-helper.js'); ?>"></script>
<script src="assets/app.js?v=<?php echo filemtime('assets/app.js'); ?>"></script>

<!-- Bouton de déconnexion discret -->
<a href="?logout=1" id="logout-btn" class="logout-discrete" title="Se déconnecter">⎋</a>

</body>
</html>
