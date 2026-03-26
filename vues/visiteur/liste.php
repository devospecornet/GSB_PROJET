<?php
$titrePage = 'Visiteur - Mes fiches';
include __DIR__ . '/../squelette/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">Mes Notes de Frais</h1>
        <p class="text-secondary mb-0">Saisissez, modifiez, transmettez et suivez vos notes de frais.</p>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?= e($typeMessage) ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="bloc-page overflow-hidden mb-4">
    <div class="entete-carte-bleue">
        <?= !empty($ficheEnModification) ? 'Modifier une note de frais' : 'Saisir une nouvelle note de frais' ?>
    </div>
    <div class="p-4">
        <div class="info-plafond mb-4">
            <h2 class="h6 fw-bold mb-2">Règles de saisie</h2>
            <div class="small text-secondary">
                Un justificatif est obligatoire pour transmettre une fiche.
                <br>Si des hors forfaits sont présents, au moins un justificatif contenant la TVA est obligatoire.
                <br>Plafonds hors forfait : petit déjeuner 12 €, repas midi 23 €, repas soir 23 €, nuitée 150 €.
            </div>
        </div>

        <form method="POST" class="row g-3 align-items-end">
            <input type="hidden" name="id_fiche" value="<?= e((string)$valeursFormulaire['id']) ?>">

            <div class="col-12 col-md-4">
                <label class="form-label">Mois et année</label>
                <input
                    type="month"
                    name="mois"
                    class="form-control"
                    value="<?= e((string)$valeursFormulaire['mois']) ?>"
                    required>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label">Essence (€)</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="frais_essence"
                    class="form-control"
                    value="<?= e((string)$valeursFormulaire['frais_essence']) ?>">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label">Hôtel / nuitée forfaitisée (€)</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="frais_hotel"
                    class="form-control"
                    value="<?= e((string)$valeursFormulaire['frais_hotel']) ?>">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label">Repas forfaitisé (€)</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="frais_resto"
                    class="form-control"
                    value="<?= e((string)$valeursFormulaire['frais_resto']) ?>">
            </div>

            <div class="col-12 d-grid">
                <button type="submit" name="enregistrer_fiche" class="btn btn-success btn-arrondi">
                    <?= !empty($ficheEnModification) ? 'Modifier la fiche' : 'Enregistrer la fiche' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="bloc-page p-4 mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Filtrer par mois / année</label>
            <input type="month" name="mois" class="form-control" value="<?= e($filtreMois) ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label">Filtrer par statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option value="saisie" <?= $filtreStatut === 'saisie' ? 'selected' : '' ?>>Brouillon</option>
                <option value="transmise" <?= $filtreStatut === 'transmise' ? 'selected' : '' ?>>Transmise</option>
                <option value="validee" <?= $filtreStatut === 'validee' ? 'selected' : '' ?>>Validée</option>
                <option value="refusee" <?= $filtreStatut === 'refusee' ? 'selected' : '' ?>>Refusée</option>
            </select>
        </div>

        <div class="col-md-3 d-grid">
            <button class="btn btn-primary btn-arrondi">Filtrer</button>
        </div>
    </form>
</div>

<div class="bloc-page overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Numéro</th>
                    <th>Mois / année</th>
                    <th>Montant total</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fiches as $fiche): ?>
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
                        <td class="text-end">
                            <div class="d-flex justify-content-end flex-wrap gap-2">
                                <a href="<?= APP_BASE_URL ?>/public/synthese.php?id=<?= (int)$fiche['id'] ?>" class="btn btn-outline-secondary btn-sm btn-arrondi">
                                    Voir détails
                                </a>

                                <?php if (in_array($fiche['statut'], ['saisie', 'refusee'], true)): ?>
                                    <a href="<?= APP_BASE_URL ?>/public/visiteur.php?modifier=<?= (int)$fiche['id'] ?>" class="btn btn-outline-dark btn-sm btn-arrondi">
                                        Modifier
                                    </a>

                                    <form method="POST">
                                        <input type="hidden" name="id_fiche" value="<?= (int)$fiche['id'] ?>">
                                        <button type="submit" name="transmettre_fiche" class="btn btn-primary btn-sm btn-arrondi">
                                            Envoyer
                                        </button>
                                    </form>

                                    <form method="POST" onsubmit="return confirm('Supprimer cette fiche ?');">
                                        <input type="hidden" name="id_fiche" value="<?= (int)$fiche['id'] ?>">
                                        <button type="submit" name="supprimer_fiche" class="btn btn-outline-danger btn-sm btn-arrondi">
                                            Supprimer
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($fiches)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary">Aucune fiche trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../squelette/footer.php'; ?>