<?php

require_once __DIR__ . '/commun.php';

verifierMethode(['POST']);

$body = lireCorpsJson();

$email = trim($body['email'] ?? '');
$motDePasse = $body['mot_de_passe'] ?? '';

if ($email === '' || $motDePasse === '') {
    reponseJson([
        'succes' => false,
        'message' => 'Email et mot de passe obligatoires.'
    ], 400);
}

$utilisateurRepository = new UtilisateurRepository();
$apiRepository = new ApiRepository();

$utilisateur = $utilisateurRepository->verifierConnexion($email, $motDePasse);

if (!$utilisateur) {
    reponseJson([
        'succes' => false,
        'message' => 'Identifiants invalides ou compte non approuvé.'
    ], 401);
}

$apiRepository->supprimerJetonsUtilisateur((int)$utilisateur['id']);
$jeton = $apiRepository->creerJeton((int)$utilisateur['id']);

reponseJson([
    'succes' => true,
    'message' => 'Connexion API réussie.',
    'data' => [
        'jeton' => $jeton,
        'expire_dans_secondes' => 900,
        'utilisateur' => [
            'id' => (int)$utilisateur['id'],
            'nom' => $utilisateur['nom'],
            'prenom' => $utilisateur['prenom'],
            'email' => $utilisateur['email'],
            'role' => $utilisateur['role']
        ]
    ]
], 200);
