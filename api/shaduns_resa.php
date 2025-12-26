<?php
header('Content-Type: application/json');
require_once '../db.php';
require_once '../csrf.php';

// Valider le token CSRF
if (!CSRF::validateRequest('shaduns')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de sécurité invalide']);
    exit;
}

$prenom_contact = $_POST['prenom_contact'] ?? '';
$nom_contact    = $_POST['nom_contact'] ?? '';
$nb_personnes   = isset($_POST['nb_personnes']) ? (int)$_POST['nb_personnes'] : 1;

// Champs obligatoires
if ($prenom_contact === '' || $nom_contact === '' || $nb_personnes < 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Merci de renseigner au minimum le prénom, le nom et le nombre de personnes.'
    ]);
    exit;
}

// On construit la liste des personnes
$personnes = [];

// Le contact principal compte comme 1 personne
$personnes[] = trim($prenom_contact . ' ' . $nom_contact);

// Les autres personnes saisies dans le formulaire (person_2, person_3, ...)
foreach ($_POST as $key => $value) {
    if (strpos($key, 'person_') === 0 && trim($value) !== '') {
        $personnes[] = trim($value); // prénom + nom dans un seul champ texte
    }
}

// Si jamais le nombre saisi ne correspond pas au nombre de noms saisis,
// on ajuste simplement au nombre réel de personnes.
if (count($personnes) < $nb_personnes) {
    $nb_personnes = count($personnes);
}

// Normalisation du nom du contact principal pour comparaison
$prenomNorm = mb_strtolower(trim($prenom_contact), 'UTF-8');
$nomNorm    = mb_strtolower(trim($nom_contact), 'UTF-8');

// Vérifier si ce contact principal a déjà réservé
$check = $pdo->prepare("
    SELECT id 
    FROM shaduns_resa_personnes
    WHERE LOWER(prenom_contact) = :prenom
      AND LOWER(nom_contact)    = :nom
    LIMIT 1
");

$check->execute([
    ':prenom' => $prenomNorm,
    ':nom'    => $nomNorm
]);

if ($check->fetch()) {

    // Mise en forme propre du prénom pour l’affichage
    $prenomAffiche = ucfirst(strtolower(trim($prenom_contact)));

    echo json_encode([
        'success' => false,
        // Message personnalisé avec saut de ligne compris
        'message' => "$prenomAffiche, ok on as dit venez nombreux mais abuses pas tu as déjà réservé une place au dortoir 😄\n\rSi tu veux modifier quelque chose, appelle-nous !"
    ]);
    exit;
}


try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO shaduns_resa_personnes (prenom_contact, nom_contact, personne_nom, created_at)
        VALUES (:prenom_contact, :nom_contact, :personne_nom, NOW())
    ");

    // On insère une ligne par personne
    for ($i = 0; $i < $nb_personnes; $i++) {
        $nomPersonne = $personnes[$i] ?? null;
        if ($nomPersonne === null || $nomPersonne === '') {
            continue;
        }

        $stmt->execute([
            ':prenom_contact' => $prenom_contact,
            ':nom_contact'    => $nom_contact,
            ':personne_nom'   => $nomPersonne,
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Merci, nous avons bien enregistré votre réservation pour le dortoir.'
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l’enregistrement. Merci de réessayer ou de nous contacter.'
    ]);
}
