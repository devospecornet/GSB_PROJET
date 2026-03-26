<?php
$titrePage = 'Comptable - Détail de la fiche';
include __DIR__ . '/../squelette/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">Détail de la fiche</h1>
        <p class="text-secondary mb-0">Analyse et traitement de la note de frais.</p>
    </div>
    <a href="<?= APP_BASE_URL ?>/public/comptable.php" class="btn btn-outline-secondary btn-arrondi">Retour</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?= e($typeMessage) ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="bloc-page p-4 mb-4">
            <p><strong>Numéro de fiche :</strong> <?= e($fiche['numero_fiche']) ?></p>
            <p><strong>Visiteur :</strong> <?= e($fiche['prenom'] . ' ' . $fiche['nom']) ?></p>
            <p><strong>Email :</strong> <?= e($fiche['email']) ?></p>
            <p><strong>Mois / année :</strong> <?= e(formaterMoisAnnee($fiche['mois'])) ?></p>
            <p><strong>Montant :</strong> <?= number_format((float)$fiche['montant_total'], 2, ',', ' ') ?> €</p>
            <p><strong>Statut :</strong> <?= e($fiche['statut']) ?></p>

            <h3 class="h6 fw-bold mt-4">Commentaire visiteur</h3>
            <div class="border rounded p-3 mb-4">
                <?= nl2br(e($fiche['commentaire_visiteur'] ?? '')) ?>
            </div>

            <h3 class="h6 fw-bold">Hors forfaits</h3>
            <?php if (empty($horsForfaits)): ?>
                <div class="text-secondary mb-4">Aucun hors forfait.</div>
            <?php else: ?>
                <div class="table-responsive mb-4">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>HT</th>
                                <th>TVA</th>
                                <th>TTC</th>
                                <th>Taux</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($horsForfaits as $horsForfait): ?>
                                <tr>
                                    <td><?= e($libellesHorsForfait[$horsForfait['type_consommation']] ?? $horsForfait['type_consommation']) ?></td>
                                    <td><?= number_format((float)$horsForfait['montant_ht'], 2, ',', ' ') ?> €</td>
                                    <td><?= number_format((float)$horsForfait['montant_tva'], 2, ',', ' ') ?> €</td>
                                    <td><?= number_format((float)$horsForfait['montant_ttc'], 2, ',', ' ') ?> €</td>
                                    <td><?= number_format((float)$horsForfait['taux_tva'], 0, ',', ' ') ?> %</td>
                                    <td><?= e($horsForfait['commentaire']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <h3 class="h6 fw-bold">Justificatifs</h3>
            <?php if (empty($justificatifs)): ?>
                <div class="text-secondary">Aucun justificatif.</div>
            <?php else: ?>
                <?php foreach ($justificatifs as $justificatif): ?>
                    <div class="border rounded p-3 mb-3">
                        <div class="fw-semibold"><?= e($justificatif['nom_reel']) ?></div>
                        <div class="small text-secondary mb-2">
                            <?= e(date('d/m/Y H:i', strtotime($justificatif['date_envoi']))) ?>
                        </div>
                        <div class="small text-secondary mb-2">
                            TVA affichée : <strong><?= ((int)$justificatif['contient_tva'] === 1) ? 'Oui' : 'Non' ?></strong>
                        </div>
                        <a href="<?= APP_BASE_URL ?>/stockage/<?= e($justificatif['nom_serveur']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            Ouvrir
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="bloc-page p-4">
            <h3 class="h5 fw-bold mb-3">Traitement</h3>

            <?php if ($fiche['statut'] === 'transmise'): ?>
                <form method="POST">
                    <input type="hidden" name="id_fiche" value="<?= (int)$fiche['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Commentaire du comptable</label>
                        <textarea name="commentaire_comptable" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button
                            type="submit"
                            name="traiter_fiche"
                            value="1"
                            onclick="document.getElementById('statut-cache').value='validee';"
                            class="btn btn-success btn-arrondi">
                            Valider
                        </button>

                        <button
                            type="submit"
                            name="traiter_fiche"
                            value="1"
                            onclick="document.getElementById('statut-cache').value='refusee';"
                            class="btn btn-outline-danger btn-arrondi">
                            Refuser
                        </button>
                    </div>

                    <input type="hidden" name="statut" id="statut-cache" value="">
                </form>
            <?php else: ?>
                <div class="alert alert-light">Cette fiche a déjà été traitée.</div>
            <?php endif; ?>

            <?php if (!empty($fiche['commentaire_comptable'])): ?>
                <hr>
                <div class="fw-semibold mb-2">Commentaire enregistré</div>
                <div><?= nl2br(e($fiche['commentaire_comptable'])) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../squelette/footer.php'; ?>