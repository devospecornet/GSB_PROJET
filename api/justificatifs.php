<?php

require_once __DIR__ . '/commun.php';

$utilisateurApi = utilisateurApiAuthentifie();
$justificatifRepository = new JustificatifRepository();
$ficheRepository = new FicheRepository();

$idUtilisateur = (int)$utilisateurApi['id_utilisateur'];
$methode = $_SERVER['REQUEST_METHOD'];

if ($methode === 'GET') {
    $idFiche = (int)($_GET['id_fiche'] ?? 0);

    if ($idFiche <= 0) {
        reponseJson([
            'succes' => false,
            'message' => 'id_fiche obligatoire.'
        ], 400);
    }

    $fiche = $ficheRepository->trouverParIdEtUtilisateur($idFiche, $idUtilisateur);

    if (!$fiche) {
        reponseJson([
            'succes' => false,
            'message' => 'Fiche introuvable.'
        ], 404);
    }

    reponseJson([
        'succes' => true,
        'justificatifs' => $justificatifRepository->getParFiche($idFiche)
    ], 200);
}

reponseJson([
    'succes' => false,
    'message' => 'Méthode non autorisée.'
], 405);
