<?php
$titrePage = 'Administration - Toutes les fiches';
include __DIR__ . '/../squelette/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">Toutes les notes de frais</h1>
        <p class="text-secondary mb-0">Vue globale, triée par utilisateur, avec filtres et statistiques.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-xl-2">
        <div class="bloc-page p-4 h-100 carte-stat carte-stat-bleue">
            <div class="petit-label">TOTAL</div>
            <div class="display-6 fw-bold"><?= (int)$statistiques['total'] ?></div>
            <div class="text-secondary">Fiches</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-2">
        <div class="bloc-page p-4 h-100 carte-stat carte-stat-verte">
            <div class="petit-label">VALIDÉES</div>
            <div class="display-6 fw-bold"><?= (int)$statistiques['validees'] ?></div>
            <div class="text-secondary">Acceptées</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-2">
        <div class="bloc-page p-4 h-100 carte-stat carte-stat-orange">
            <div class="petit-label">TRANSMISES</div>
            <div class="display-6 fw-bold"><?= (int)$statistiques['transmises'] ?></div>
            <div class="text-secondary">En attente</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-2">
        <div class="bloc-page p-4 h-100 carte-stat carte-stat-rouge">
            <div class="petit-label">REFUSÉES</div>
            <div class="display-6 fw-bold"><?= (int)$statistiques['refusees'] ?></div>
            <div class="text-secondary">Rejetées</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-2">
        <div class="bloc-page p-4 h-100 carte-stat">
            <div class="petit-label">BROUILLONS</div>
            <div class="display-6 fw-bold"><?= (int)$statistiques['saisies'] ?></div>
            <div class="text-secondary">À compléter</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-2">
        <div class="bloc-page p-4 h-100 carte-stat carte-stat-verte">
            <div class="petit-label">MONTANT VALIDÉ</div>
            <div class="h3 fw-bold mb-0"><?= number_format((float)$statistiques['montant_valide'], 2, ',', ' ') ?> €</div>
            <div class="text-secondary">Total remboursable</div>
        </div>
    </div>
</div>

<div class="bloc-page p-4 mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Nom / prénom</label>
            <input type="text" name="nom" class="form-control" value="<?= e($nom) ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-control" value="<?= e($email) ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label">Mois / année</label>
            <input type="month" name="mois" class="form-control" value="<?= e($mois) ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option value="saisie" <?= $statut === 'saisie' ? 'selected' : '' ?>>Saisie</option>
                <option value="transmise" <?= $statut === 'transmise' ? 'selected' : '' ?>>Transmise</option>
                <option value="validee" <?= $statut === 'validee' ? 'selected' : '' ?>>Validée</option>
                <option value="refusee" <?= $statut === 'refusee' ? 'selected' : '' ?>>Refusée</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary btn-arrondi">Filtrer</button>
        </div>
    </form>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="bloc-page p-4">
            <h2 class="h4 fw-bold mb-3">Top utilisateurs</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Nombre de fiches</th>
                            <th>Total montants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statistiques['top_utilisateurs'] as $utilisateur): ?>
                            <tr>
                                <td class="fw-semibold"><?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></td>
                                <td><?= e($utilisateur['email']) ?></td>
                                <td><?= (int)$utilisateur['nombre_fiches'] ?></td>
                                <td><?= number_format((float)$utilisateur['total_montants'], 2, ',', ' ') ?> €</td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($statistiques['top_utilisateurs'])): ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary">Aucune donnée disponible.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (empty($fichesParUtilisateur)): ?>
    <div class="bloc-page p-4 text-secondary">Aucune fiche trouvée.</div>
<?php else: ?>
    <?php foreach ($fichesParUtilisateur as $cleUtilisateur => $listeFiches): ?>
        <?php $premiereFiche = $listeFiches[0]; ?>

        <div class="bloc-page p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h2 class="h4 fw-bold mb-1"><?= e($premiereFiche['prenom'] . ' ' . $premiereFiche['nom']) ?></h2>
                    <p class="text-secondary mb-0"><?= e($premiereFiche['email']) ?></p>
                </div>
                <span class="badge-role">Nombre de fiches : <?= count($listeFiches) ?></span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Numéro</th>
                            <th>Mois / année</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date modification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listeFiches as $fiche): ?>
                            <?php
                            $classeBadge = match ($fiche['statut']) {
                                'transmise' => 'badge-transmise',
                                'validee' => 'badge-validee',
                                'refusee' => 'badge-refusee',
                                default => 'badge-saisie'
                            };
                            ?>
                            <tr>
                                <td class="fw-semibold"><?= e($fiche['numero_fiche']) ?></td>
                                <td><?= e(formaterMoisAnnee($fiche['mois'])) ?></td>
                                <td><?= number_format((float)$fiche['montant_total'], 2, ',', ' ') ?> €</td>
                                <td>
                                    <span class="badge-statut <?= $classeBadge ?>">
                                        <?= e($fiche['statut']) ?>
                                    </span>
                                </td>
                                <td><?= e(date('d/m/Y H:i', strtotime($fiche['date_modification']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../squelette/footer.php'; ?>