<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../modeles/FicheRepository.php';

exigerConnexion(['visiteur', 'comptable', 'administrateur']);

$utilisateur = $_SESSION['utilisateur'];
$ficheRepository = new FicheRepository();

$statistiques = [
    'total' => $ficheRepository->compterToutesLesFiches(),
    'saisie' => $ficheRepository->compterParStatut('saisie'),
    'transmise' => $ficheRepository->compterParStatut('transmise'),
    'validee' => $ficheRepository->compterParStatut('validee'),
    'refusee' => $ficheRepository->compterParStatut('refusee'),
    'montants_valides' => $ficheRepository->sommeMontantsValides(),
];

$titrePage = 'Tableau de bord';
include __DIR__ . '/../vues/squelette/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">Tableau de bord</h1>
        <p class="text-secondary mb-0">
            Bonjour <?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?>,
            vous êtes connecté en tant que <strong><?= e($utilisateur['role']) ?></strong>.
        </p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="bloc-page p-4 h-100">
            <div class="text-uppercase petit-label">Fiches totales</div>
            <div class="display-6 fw-bold"><?= (int)$statistiques['total'] ?></div>
            <div class="text-secondary">Toutes les notes enregistrées</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="bloc-page p-4 h-100">
            <div class="text-uppercase petit-label">Brouillons</div>
            <div class="display-6 fw-bold"><?= (int)$statistiques['saisie'] ?></div>
            <div class="text-secondary">Fiches encore en saisie</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="bloc-page p-4 h-100">
            <div class="text-uppercase petit-label">Transmises</div>
            <div class="display-6 fw-bold"><?= (int)$statistiques['transmise'] ?></div>
            <div class="text-secondary">En attente de traitement</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="bloc-page p-4 h-100">
            <div class="text-uppercase petit-label">Montants validés</div>
            <div class="display-6 fw-bold"><?= number_format((float)$statistiques['montants_valides'], 2, ',', ' ') ?> €</div>
            <div class="text-secondary">
                Validées : <?= (int)$statistiques['validee'] ?> · Refusées : <?= (int)$statistiques['refusee'] ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <?php if ($utilisateur['role'] === 'visiteur'): ?>
        <div class="col-12 col-md-6">
            <div class="bloc-page p-4 h-100">
                <h2 class="h4 fw-bold">Mes notes de frais</h2>
                <p class="text-secondary">Créer, modifier, ajouter les justificatifs, gérer les hors forfaits et transmettre vos fiches.</p>
                <a href="<?= APP_BASE_URL ?>/public/visiteur.php" class="btn btn-primary btn-arrondi">Accéder à mes fiches</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($utilisateur['role'] === 'comptable'): ?>
        <div class="col-12 col-md-6">
            <div class="bloc-page p-4 h-100">
                <h2 class="h4 fw-bold">Validation comptable</h2>
                <p class="text-secondary">Consulter les fiches transmises et les traiter.</p>
                <a href="<?= APP_BASE_URL ?>/public/comptable.php" class="btn btn-primary btn-arrondi">Accéder aux fiches</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($utilisateur['role'] === 'administrateur'): ?>
        <div class="col-12 col-md-6">
            <div class="bloc-page p-4 h-100">
                <h2 class="h4 fw-bold">Administration</h2>
                <p class="text-secondary">Gérer les utilisateurs et superviser le système.</p>
                <a href="<?= APP_BASE_URL ?>/public/admin.php" class="btn btn-primary btn-arrondi">Accéder à l’administration</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../vues/squelette/footer.php'; ?>