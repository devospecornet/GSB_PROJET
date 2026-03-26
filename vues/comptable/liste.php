<?php
$titrePage = 'Comptable - Liste des fiches';
include __DIR__ . '/../squelette/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">Traitement des fiches</h1>
        <p class="text-secondary mb-0">Consultez et traitez les fiches de frais transmises.</p>
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

<div class="bloc-page overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Numéro</th>
                    <th>Visiteur</th>
                    <th>Email</th>
                    <th>Mois / année</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th class="text-end">Action</th>
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
                        <td><?= e($fiche['prenom'] . ' ' . $fiche['nom']) ?></td>
                        <td><?= e($fiche['email']) ?></td>
                        <td><?= e(formaterMoisAnnee($fiche['mois'])) ?></td>
                        <td><?= number_format((float)$fiche['montant_total'], 2, ',', ' ') ?> €</td>
                        <td>
                            <span class="badge-statut <?= $classeBadge ?>">
                                <?= e($fiche['statut']) ?>
                            </span>
                        </td>
                        <td><?= e(date('d/m/Y H:i', strtotime($fiche['date_modification']))) ?></td>
                        <td class="text-end">
                            <a href="<?= APP_BASE_URL ?>/public/detail_fiche.php?id=<?= (int)$fiche['id'] ?>" class="btn btn-outline-primary btn-sm btn-arrondi">
                                Consulter
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($fiches)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-secondary">Aucune fiche trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../squelette/footer.php'; ?>