<?php

require_once __DIR__ . '/commun.php';

$utilisateurApi = utilisateurApiAuthentifie();
$horsForfaitRepository = new HorsForfaitRepository();
$ficheRepository = new FicheRepository();
$justificatifRepository = new JustificatifRepository();

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
        'hors_forfaits' => $horsForfaitRepository->getParFiche($idFiche)
    ], 200);
}

if ($methode === 'POST') {
    $donnees = lireCorpsJson();

    $idFiche = (int)($donnees['id_fiche'] ?? 0);
    $typeConsommation = trim($donnees['type_consommation'] ?? '');
    $montantTtc = max(0, (float)($donnees['montant_ttc'] ?? 0));
    $commentaire = trim($donnees['commentaire'] ?? '');
    $tauxTva = (float)($donnees['taux_tva'] ?? 0);

    $fiche = $ficheRepository->trouverParIdEtUtilisateur($idFiche, $idUtilisateur);

    if (!$fiche) {
        reponseJson([
            'succes' => false,
            'message' => 'Fiche introuvable.'
        ], 404);
    }

    if (!in_array($fiche['statut'], ['saisie', 'refusee'], true)) {
        reponseJson([
            'succes' => false,
            'message' => 'Cette fiche ne peut plus être modifiée.'
        ], 400);
    }

    if (!$justificatifRepository->existeAvecTva($idFiche)) {
        reponseJson([
            'succes' => false,
            'message' => 'Ajoutez d’abord un justificatif contenant la TVA.'
        ], 400);
    }

    if (!in_array($typeConsommation, ['petit_dejeuner', 'repas_midi', 'repas_soir', 'nuitee'], true)) {
        reponseJson([
            'succes' => false,
            'message' => 'Type de consommation invalide.'
        ], 400);
    }

    if ($commentaire === '') {
        reponseJson([
            'succes' => false,
            'message' => 'Commentaire obligatoire.'
        ], 400);
    }

    if (!$horsForfaitRepository->tauxTvaAutorise($tauxTva)) {
        reponseJson([
            'succes' => false,
            'message' => 'Le taux TVA doit être 5, 10 ou 20.'
        ], 400);
    }

    $plafond = $horsForfaitRepository->montantMaximum($typeConsommation);
    if ($montantTtc <= 0 || $montantTtc > $plafond) {
        reponseJson([
            'succes' => false,
            'message' => 'Montant hors forfait invalide par rapport au plafond.'
        ], 400);
    }

    $ok = $horsForfaitRepository->ajouter($idFiche, $typeConsommation, $montantTtc, $commentaire, $tauxTva);

    if (!$ok) {
        reponseJson([
            'succes' => false,
            'message' => 'Erreur lors de l’ajout.'
        ], 500);
    }

    $ficheRepository->recalculerMontantTotal($idFiche);

    reponseJson([
        'succes' => true,
        'message' => 'Hors forfait ajouté.'
    ], 201);
}

if ($methode === 'PUT') {
    $donnees = lireCorpsJson();

    $idHorsForfait = (int)($donnees['id'] ?? 0);
    $idFiche = (int)($donnees['id_fiche'] ?? 0);
    $typeConsommation = trim($donnees['type_consommation'] ?? '');
    $montantTtc = max(0, (float)($donnees['montant_ttc'] ?? 0));
    $commentaire = trim($donnees['commentaire'] ?? '');
    $tauxTva = (float)($donnees['taux_tva'] ?? 0);

    if ($idHorsForfait <= 0 || $idFiche <= 0) {
        reponseJson([
            'succes' => false,
            'message' => 'id et id_fiche obligatoires.'
        ], 400);
    }

    $fiche = $ficheRepository->trouverParIdEtUtilisateur($idFiche, $idUtilisateur);

    if (!$fiche) {
        reponseJson([
            'succes' => false,
            'message' => 'Fiche introuvable.'
        ], 404);
    }

    if (!in_array($fiche['statut'], ['saisie', 'refusee'], true)) {
        reponseJson([
            'succes' => false,
            'message' => 'Cette fiche ne peut plus être modifiée.'
        ], 400);
    }

    if (!$justificatifRepository->existeAvecTva($idFiche)) {
        reponseJson([
            'succes' => false,
            'message' => 'Ajoutez d’abord un justificatif contenant la TVA.'
        ], 400);
    }

    if (!in_array($typeConsommation, ['petit_dejeuner', 'repas_midi', 'repas_soir', 'nuitee'], true)) {
        reponseJson([
            'succes' => false,
            'message' => 'Type de consommation invalide.'
        ], 400);
    }

    if ($commentaire === '') {
        reponseJson([
            'succes' => false,
            'message' => 'Commentaire obligatoire.'
        ], 400);
    }

    if (!$horsForfaitRepository->tauxTvaAutorise($tauxTva)) {
        reponseJson([
            'succes' => false,
            'message' => 'Le taux TVA doit être 5, 10 ou 20.'
        ], 400);
    }

    $plafond = $horsForfaitRepository->montantMaximum($typeConsommation);
    if ($montantTtc <= 0 || $montantTtc > $plafond) {
        reponseJson([
            'succes' => false,
            'message' => 'Montant hors forfait invalide par rapport au plafond.'
        ], 400);
    }

    $ok = $horsForfaitRepository->modifier($idHorsForfait, $idFiche, $typeConsommation, $montantTtc, $commentaire, $tauxTva);

    if (!$ok) {
        reponseJson([
            'succes' => false,
            'message' => 'Modification impossible.'
        ], 400);
    }

    $ficheRepository->recalculerMontantTotal($idFiche);

    reponseJson([
        'succes' => true,
        'message' => 'Hors forfait modifié.'
    ], 200);
}

if ($methode === 'DELETE') {
    $donnees = lireCorpsJson();
    $idHorsForfait = (int)($donnees['id'] ?? 0);
    $idFiche = (int)($donnees['id_fiche'] ?? 0);

    if ($idHorsForfait <= 0 || $idFiche <= 0) {
        reponseJson([
            'succes' => false,
            'message' => 'id et id_fiche obligatoires.'
        ], 400);
    }

    $fiche = $ficheRepository->trouverParIdEtUtilisateur($idFiche, $idUtilisateur);

    if (!$fiche) {
        reponseJson([
            'succes' => false,
            'message' => 'Fiche introuvable.'
        ], 404);
    }

    if (!in_array($fiche['statut'], ['saisie', 'refusee'], true)) {
        reponseJson([
            'succes' => false,
            'message' => 'Cette fiche ne peut plus être modifiée.'
        ], 400);
    }

    $ok = $horsForfaitRepository->supprimer($idHorsForfait, $idFiche);

    if (!$ok) {
        reponseJson([
            'succes' => false,
            'message' => 'Suppression impossible.'
        ], 400);
    }

    $ficheRepository->recalculerMontantTotal($idFiche);

    reponseJson([
        'succes' => true,
        'message' => 'Hors forfait supprimé.'
    ], 200);
}

reponseJson([
    'succes' => false,
    'message' => 'Méthode non autorisée.'
], 405);
