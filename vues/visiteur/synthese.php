<?php
$titrePage = 'Visiteur - Synthèse';
include __DIR__ . '/../squelette/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">Synthèse de la fiche</h1>
        <p class="text-secondary mb-0">Consultez le détail de votre note de frais.</p>
    </div>
    <a href="<?= APP_BASE_URL ?>/public/visiteur.php" class="btn btn-outline-secondary btn-arrondi">Retour</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?= e($typeMessage) ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="bloc-page p-4 mb-4">
            <p><strong>Numéro de fiche :</strong> <?= e($fiche['numero_fiche']) ?></p>
            <p><strong>Mois / année :</strong> <?= e(formaterMoisAnnee($fiche['mois'])) ?></p>
            <p><strong>Montant forfait :</strong> <?= number_format((float)$montantForfait, 2, ',', ' ') ?> €</p>
            <p><strong>Montant hors forfait TTC :</strong> <?= number_format((float)$montantHorsForfait, 2, ',', ' ') ?> €</p>
            <p><strong>Montant total :</strong> <?= number_format((float)$fiche['montant_total'], 2, ',', ' ') ?> €</p>
            <p><strong>Statut :</strong> <?= e($fiche['statut']) ?></p>

            <h3 class="h6 fw-bold mt-4">Commentaire visiteur</h3>
            <div class="border rounded p-3">
                <?= nl2br(e($fiche['commentaire_visiteur'] ?? '')) ?>
            </div>

            <?php if (!empty($fiche['commentaire_comptable'])): ?>
                <h3 class="h6 fw-bold mt-4">Commentaire comptable</h3>
                <div class="alert alert-warning">
                    <?= nl2br(e($fiche['commentaire_comptable'])) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="bloc-page p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 fw-bold mb-0">Gestion des hors forfaits</h3>
                <span class="badge-role">Justificatif TVA obligatoire</span>
            </div>

            <div class="info-plafond mb-4">
                <div class="fw-bold mb-2">Plafonds autorisés</div>
                <div class="small text-secondary">
                    Petit déjeuner : 12,00 € maximum
                    <br>Repas du midi : 23,00 € maximum
                    <br>Repas du soir : 23,00 € maximum
                    <br>Nuitée : 150,00 € maximum
                    <br>Taux TVA autorisés : 5 %, 10 %, 20 %
                </div>
            </div>

            <?php if (in_array($fiche['statut'], ['saisie', 'refusee'], true)): ?>
                <form method="POST" class="row g-3 align-items-end mb-4">
                    <input type="hidden" name="id_fiche" value="<?= (int)$fiche['id'] ?>">

                    <?php if ($horsForfaitEnEdition): ?>
                        <input type="hidden" name="id_hors_forfait" value="<?= (int)$horsForfaitEnEdition['id'] ?>">
                    <?php endif; ?>

                    <div class="col-md-3">
                        <label class="form-label">Type de consommation</label>
                        <select name="type_consommation" class="form-select" required>
                            <option value="">Choisir</option>
                            <option value="petit_dejeuner" <?= ($horsForfaitEnEdition['type_consommation'] ?? '') === 'petit_dejeuner' ? 'selected' : '' ?>>Petit déjeuner</option>
                            <option value="repas_midi" <?= ($horsForfaitEnEdition['type_consommation'] ?? '') === 'repas_midi' ? 'selected' : '' ?>>Repas du midi</option>
                            <option value="repas_soir" <?= ($horsForfaitEnEdition['type_consommation'] ?? '') === 'repas_soir' ? 'selected' : '' ?>>Repas du soir</option>
                            <option value="nuitee" <?= ($horsForfaitEnEdition['type_consommation'] ?? '') === 'nuitee' ? 'selected' : '' ?>>Nuitée</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Montant TTC</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="montant_hors_forfait"
                            class="form-control"
                            value="<?= e(isset($horsForfaitEnEdition['montant_ttc']) ? (string)$horsForfaitEnEdition['montant_ttc'] : '') ?>"
                            required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">TVA</label>
                        <select name="taux_tva" class="form-select" required>
                            <?php $tauxActuel = isset($horsForfaitEnEdition['taux_tva']) ? (string)((float)$horsForfaitEnEdition['taux_tva']) : ''; ?>
                            <option value="">Choisir</option>
                            <option value="5" <?= $tauxActuel === '5' ? 'selected' : '' ?>>5 %</option>
                            <option value="10" <?= $tauxActuel === '10' ? 'selected' : '' ?>>10 %</option>
                            <option value="20" <?= $tauxActuel === '20' ? 'selected' : '' ?>>20 %</option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Commentaire</label>
                        <input
                            type="text"
                            name="commentaire_hors_forfait"
                            class="form-control"
                            value="<?= e($horsForfaitEnEdition['commentaire'] ?? '') ?>"
                            required>
                    </div>

                    <div class="col-12 d-grid gap-2">
                        <?php if ($horsForfaitEnEdition): ?>
                            <button type="submit" name="modifier_hors_forfait" class="btn btn-primary btn-arrondi">
                                Enregistrer la modification
                            </button>
                            <button type="submit" name="annuler_edition_hors_forfait" class="btn btn-outline-secondary btn-arrondi">
                                Annuler
                            </button>
                        <?php else: ?>
                            <button type="submit" name="ajouter_hors_forfait" class="btn btn-primary btn-arrondi">
                                Ajouter le hors forfait
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>

            <?php if (empty($horsForfaits)): ?>
                <div class="text-secondary">Aucun hors forfait enregistré.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>HT</th>
                                <th>TVA</th>
                                <th>TTC</th>
                                <th>Taux</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
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
                                    <td><?= e(date('d/m/Y H:i', strtotime($horsForfait['date_ajout']))) ?></td>
                                    <td class="text-end">
                                        <?php if (in_array($fiche['statut'], ['saisie', 'refusee'], true)): ?>
                                            <a href="<?= APP_BASE_URL ?>/public/synthese.php?id=<?= (int)$fiche['id'] ?>&edit_hf=<?= (int)$horsForfait['id'] ?>" class="btn btn-outline-primary btn-sm btn-arrondi">
                                                Modifier
                                            </a>

                                            <form method="POST" onsubmit="return confirm('Supprimer ce hors forfait ?');" class="d-inline">
                                                <input type="hidden" name="id_fiche" value="<?= (int)$fiche['id'] ?>">
                                                <input type="hidden" name="id_hors_forfait" value="<?= (int)$horsForfait['id'] ?>">
                                                <button type="submit" name="supprimer_hors_forfait" class="btn btn-outline-danger btn-sm btn-arrondi">
                                                    Supprimer
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-secondary small">Lecture seule</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="bloc-page p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 fw-bold mb-0">Justificatifs</h3>
                <span class="badge-role"><?= count($justificatifs) ?> fichier(s)</span>
            </div>

            <div class="small text-secondary mb-3">
                Si la fiche contient des hors forfaits, au moins un justificatif doit contenir la TVA.
            </div>

            <?php if (in_array($fiche['statut'], ['saisie', 'refusee'], true)): ?>
                <form method="POST" enctype="multipart/form-data" class="mb-4">
                    <input type="hidden" name="id_fiche" value="<?= (int)$fiche['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Ajouter une facture / justificatif</label>
                        <input type="file" name="justificatif" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="contient_tva" value="1" id="contient_tva">
                        <label class="form-check-label" for="contient_tva">
                            Ce justificatif affiche la TVA
                        </label>
                    </div>

                    <button type="submit" name="ajouter_justificatif" class="btn btn-primary btn-arrondi w-100">
                        Ajouter le justificatif
                    </button>
                </form>
            <?php endif; ?>

            <?php if (empty($justificatifs)): ?>
                <div class="alert alert-warning mb-0">
                    Aucun justificatif ajouté pour le moment.
                </div>
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

                        <?php if (in_array($fiche['statut'], ['saisie', 'refusee'], true)): ?>
                            <form method="POST" class="mt-2" onsubmit="return confirm('Supprimer ce justificatif ?');">
                                <input type="hidden" name="id_fiche" value="<?= (int)$fiche['id'] ?>">
                                <input type="hidden" name="id_justificatif" value="<?= (int)$justificatif['id'] ?>">
                                <button type="submit" name="supprimer_justificatif" class="btn btn-outline-danger btn-sm btn-arrondi">
                                    Supprimer
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../squelette/footer.php'; ?>