<?php

require_once __DIR__ . '/commun.php';

$utilisateurApi = utilisateurApiAuthentifie();
$ficheRepository = new FicheRepository();

$idUtilisateur = (int)$utilisateurApi['id_utilisateur'];
$methode = $_SERVER['REQUEST_METHOD'];

if ($methode === 'GET') {
    $idFiche = (int)($_GET['id'] ?? 0);

    if ($idFiche > 0) {
        $fiche = $ficheRepository->trouverParIdEtUtilisateur($idFiche, $idUtilisateur);

        if (!$fiche) {
            reponseJson([
                'succes' => false,
                'message' => 'Fiche introuvable.'
            ], 404);
        }

        reponseJson([
            'succes' => true,
            'fiche' => $fiche
        ], 200);
    }

    $mois = trim($_GET['mois'] ?? '');
    $statut = trim($_GET['statut'] ?? '');

    $fiches = $ficheRepository->getFichesVisiteur(
        $idUtilisateur,
        $mois !== '' ? $mois : null,
        $statut !== '' ? $statut : null
    );

    reponseJson([
        'succes' => true,
        'fiches' => $fiches
    ], 200);
}

if ($methode === 'POST') {
    $donnees = lireCorpsJson();

    $mois = trim($donnees['mois'] ?? '');
    $fraisEssence = max(0, (float)($donnees['frais_essence'] ?? 0));
    $fraisHotel = max(0, (float)($donnees['frais_hotel'] ?? 0));
    $fraisResto = max(0, (float)($donnees['frais_resto'] ?? 0));

    if ($mois === '') {
        reponseJson([
            'succes' => false,
            'message' => 'Le mois est obligatoire.'
        ], 400);
    }

    if ($ficheRepository->ficheDuMoisExiste($idUtilisateur, $mois)) {
        reponseJson([
            'succes' => false,
            'message' => 'Une fiche existe déjà pour ce mois.'
        ], 409);
    }

    $montantTotal = $fraisEssence + $fraisHotel + $fraisResto;
    $commentaireVisiteur = "Essence : {$fraisEssence} € | Hôtel : {$fraisHotel} € | Resto : {$fraisResto} €";

    $creation = $ficheRepository->creer($idUtilisateur, $mois, $montantTotal, $commentaireVisiteur);

    if (!$creation) {
        reponseJson([
            'succes' => false,
            'message' => 'Erreur lors de la création de la fiche.'
        ], 500);
    }

    reponseJson([
        'succes' => true,
        'message' => 'Fiche créée avec succès.'
    ], 201);
}

if ($methode === 'PUT') {
    $donnees = lireCorpsJson();

    $idFiche = (int)($donnees['id'] ?? 0);
    $mois = trim($donnees['mois'] ?? '');

    if ($idFiche <= 0 || $mois === '') {
        reponseJson([
            'succes' => false,
            'message' => 'Id et mois obligatoires.'
        ], 400);
    }

    $fraisEssence = max(0, (float)($donnees['frais_essence'] ?? 0));
    $fraisHotel = max(0, (float)($donnees['frais_hotel'] ?? 0));
    $fraisResto = max(0, (float)($donnees['frais_resto'] ?? 0));

    if ($ficheRepository->ficheDuMoisExiste($idUtilisateur, $mois, $idFiche)) {
        reponseJson([
            'succes' => false,
            'message' => 'Une autre fiche existe déjà pour ce mois.'
        ], 409);
    }

    $montantTotal = $fraisEssence + $fraisHotel + $fraisResto;
    $commentaireVisiteur = "Essence : {$fraisEssence} € | Hôtel : {$fraisHotel} € | Resto : {$fraisResto} €";

    $modification = $ficheRepository->modifier($idFiche, $idUtilisateur, $mois, $montantTotal, $commentaireVisiteur);

    if (!$modification) {
        reponseJson([
            'succes' => false,
            'message' => 'Modification impossible.'
        ], 400);
    }

    $ficheRepository->recalculerMontantTotal($idFiche);

    reponseJson([
        'succes' => true,
        'message' => 'Fiche modifiée avec succès.'
    ], 200);
}

if ($methode === 'DELETE') {
    $donnees = lireCorpsJson();
    $idFiche = (int)($donnees['id'] ?? 0);

    if ($idFiche <= 0) {
        reponseJson([
            'succes' => false,
            'message' => 'Id obligatoire.'
        ], 400);
    }

    $suppression = $ficheRepository->supprimer($idFiche, $idUtilisateur);

    if (!$suppression) {
        reponseJson([
            'succes' => false,
            'message' => 'Suppression impossible.'
        ], 400);
    }

    reponseJson([
        'succes' => true,
        'message' => 'Fiche supprimée.'
    ], 200);
}

reponseJson([
    'succes' => false,
    'message' => 'Méthode non autorisée.'
], 405);
