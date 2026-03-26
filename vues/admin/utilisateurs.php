<?php
$titrePage = 'Administration - Utilisateurs';
include __DIR__ . '/../squelette/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">Gestion des utilisateurs</h1>
        <p class="text-secondary mb-0">Créer et supprimer des comptes visiteurs ou comptables.</p>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?= e($typeMessage) ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="bloc-page p-4 mb-4">
    <h2 class="h4 fw-bold mb-3">Créer un utilisateur</h2>

    <form method="POST" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Mot de passe</label>
            <input type="text" name="mot_de_passe" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Rôle</label>
            <select name="role" class="form-select">
                <option value="visiteur">Visiteur</option>
                <option value="comptable">Comptable</option>
            </select>
        </div>

        <div class="col-md-3 d-grid">
            <label class="form-label invisible">Créer</label>
            <button type="submit" name="creer_utilisateur" class="btn btn-primary btn-arrondi">Créer l'utilisateur</button>
        </div>
    </form>
</div>

<div class="bloc-page overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Approuvé</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($utilisateur['nom']) ?></td>
                        <td><?= e($utilisateur['prenom']) ?></td>
                        <td><?= e($utilisateur['email']) ?></td>
                        <td>
                            <span class="badge-role"><?= e($utilisateur['role']) ?></span>
                        </td>
                        <td><?= (int)$utilisateur['est_approuve'] === 1 ? 'Oui' : 'Non' ?></td>
                        <td class="text-end">
                            <?php if ((int)$utilisateur['id'] !== (int)$_SESSION['utilisateur']['id']): ?>
                                <form method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?');" class="d-inline">
                                    <input type="hidden" name="id_utilisateur" value="<?= (int)$utilisateur['id'] ?>">
                                    <button type="submit" name="supprimer_utilisateur" class="btn btn-outline-danger btn-sm btn-arrondi">
                                        Supprimer
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-secondary small">Compte courant</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($utilisateurs)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary">Aucun utilisateur trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../squelette/footer.php'; ?>