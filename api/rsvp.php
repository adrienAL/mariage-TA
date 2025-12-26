<?php
header('Content-Type: application/json');
require_once '../db.php';
require_once '../csrf.php';

// Valider le token CSRF
if (!CSRF::validateRequest('rsvp')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de sécurité invalide']);
    exit;
}

$prenom = $_POST['prenom'] ?? '';
$nom = $_POST['nom'] ?? '';
$presence = $_POST['presence'] ?? '';
$nb_personnes = $_POST['nb_personnes'] ?? 1;
$brunch = $_POST['brunch'] ?? 'non';
$message = $_POST['message'] ?? '';

// pas d'email dans le formulaire => on force une chaîne vide (ou une valeur par défaut)
$email = '';

// Champs obligatoires sans email
if ($prenom === '' || $nom === '' || $presence === '') {
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants']);
    exit;
}

// Récupération des prénoms supplémentaires
$autresPrenoms = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'other_firstname_') === 0 && trim($value) !== '') {
        $autresPrenoms[] = trim($value);
    }
}

// On ajoute les autres prénoms au message pour les garder en base
if (!empty($autresPrenoms)) {
    $extra = 'Autres personnes : ' . implode(', ', $autresPrenoms);
    if ($message !== '') {
        $message .= ' | ' . $extra;
    } else {
        $message = $extra;
    }
}

// Normalisation pour la recherche (on ne touche pas aux valeurs affichées)
$prenomNorm = mb_strtolower(trim($prenom), 'UTF-8');
$nomNorm    = mb_strtolower(trim($nom), 'UTF-8');

// 1) Vérifier si cette personne a déjà répondu
$check = $pdo->prepare("
    SELECT id 
    FROM rsvps 
    WHERE LOWER(prenom) = :prenom
      AND LOWER(nom)    = :nom
    LIMIT 1
");
$check->execute([
    ':prenom' => $prenomNorm,
    ':nom'    => $nomNorm,
]);

if ($check->fetch()) {
    // Déjà présent en base : on bloque la nouvelle soumission
    $message = "On sait que tu veux absolument venir mais du calme <b>$prenom</b>, on a déjà ta réponse.<br>Après tu peux toujours nous appeler, ça fait plaisir 😊";
    echo json_encode([
        'success' => false,
        'message' => $message 

    ]);
    exit;
}

// 2) Si on arrive ici : première réponse pour ce prénom/nom -> on insère
$stmt = $pdo->prepare("
    INSERT INTO rsvps (prenom, nom, email, presence, nb_personnes, brunch, message, created_at)
    VALUES (:prenom, :nom, :email, :presence, :nb_personnes, :brunch, :message, NOW())
");
$stmt->execute([
    ':prenom'       => $prenom,
    ':nom'          => $nom,
    ':email'        => $email,
    ':presence'     => $presence,
    ':nb_personnes' => $nb_personnes,
    ':brunch'       => $brunch,
    ':message'      => $message
]);

echo json_encode([
    'success' => true,
    'message' => 'Merci, nous avons bien enregistré ta réponse ❤️'
]);

