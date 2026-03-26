<?php
if (!isset($titrePage)) {
    $titrePage = 'GSB Frais POO';
}

$utilisateurSession = $_SESSION['utilisateur'] ?? null;
$roleSession = $utilisateurSession['role'] ?? '';
$pageActuelle = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titrePage) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(180deg, #f4f7fb 0%, #edf2f8 100%);
            font-family: Arial, Helvetica, sans-serif;
            color: #24324a;
        }

        .barre-haut {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e6ebf2;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-gsb {
            font-weight: 800;
            color: #1769ff;
            text-decoration: none;
            font-size: 1.7rem;
            letter-spacing: -0.03em;
        }

        .bloc-page {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(31, 45, 61, 0.08);
            border: 1px solid #eef2f6;
        }

        .entete-carte-bleue {
            background: linear-gradient(135deg, #1769ff, #4d8cff);
            color: #ffffff;
            border-radius: 20px 20px 0 0;
            padding: 1rem 1.2rem;
            font-weight: 700;
        }

        .badge-role {
            background: linear-gradient(135deg, #edf1ff, #e6f0ff);
            color: #3f51d7;
            border-radius: 999px;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 800;
            border: 1px solid #d7e4ff;
        }

        .badge-statut {
            border-radius: 999px;
            padding: 0.42rem 0.8rem;
            font-size: 0.72rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .badge-saisie {
            background: #eef2f7;
            color: #5b6678;
        }

        .badge-transmise {
            background: #e7f0ff;
            color: #1769ff;
        }

        .badge-validee {
            background: #e7f8ef;
            color: #198754;
        }

        .badge-refusee {
            background: #fdecec;
            color: #dc3545;
        }

        .btn-arrondi {
            border-radius: 999px;
            font-weight: 700;
        }

        .nav-pilule {
            border-radius: 999px;
            padding: 0.65rem 1rem;
            color: #51607a;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .nav-pilule.active,
        .nav-pilule:hover {
            background: #edf4ff;
            color: #1769ff;
            transform: translateY(-1px);
        }

        .petit-label {
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #6a768b;
        }

        .carte-stat {
            position: relative;
            overflow: hidden;
        }

        .carte-stat::after {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            top: -45px;
            right: -45px;
            opacity: 0.12;
        }

        .carte-stat-bleue::after {
            background: #1769ff;
        }

        .carte-stat-verte::after {
            background: #198754;
        }

        .carte-stat-orange::after {
            background: #fd7e14;
        }

        .carte-stat-rouge::after {
            background: #dc3545;
        }

        .info-plafond {
            background: #f8fbff;
            border: 1px dashed #cfe0ff;
            border-radius: 16px;
            padding: 1rem;
        }

        .table thead th {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #66758f;
        }
    </style>
</head>

<body>

    <nav class="barre-haut py-3 mb-4">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-4">
                <a href="<?= APP_BASE_URL ?>/public/tableau_bord.php" class="logo-gsb">GSB Frais</a>

                <?php if ($utilisateurSession): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="nav-pilule <?= $pageActuelle === 'tableau_bord.php' ? 'active' : '' ?>" href="<?= APP_BASE_URL ?>/public/tableau_bord.php">Tableau de bord</a>

                        <?php if ($roleSession === 'visiteur'): ?>
                            <a class="nav-pilule <?= $pageActuelle === 'visiteur.php' || $pageActuelle === 'synthese.php' ? 'active' : '' ?>" href="<?= APP_BASE_URL ?>/public/visiteur.php">Mes fiches</a>
                        <?php endif; ?>

                        <?php if (in_array($roleSession, ['comptable', 'administrateur'], true)): ?>
                            <a class="nav-pilule <?= $pageActuelle === 'comptable.php' || $pageActuelle === 'detail_fiche.php' ? 'active' : '' ?>" href="<?= APP_BASE_URL ?>/public/comptable.php">Validation</a>
                        <?php endif; ?>

                        <?php if ($roleSession === 'administrateur'): ?>
                            <a class="nav-pilule <?= $pageActuelle === 'admin.php' ? 'active' : '' ?>" href="<?= APP_BASE_URL ?>/public/admin.php">Utilisateurs</a>
                            <a class="nav-pilule <?= $pageActuelle === 'toutes_les_fiches.php' ? 'active' : '' ?>" href="<?= APP_BASE_URL ?>/public/toutes_les_fiches.php">Toutes les fiches</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($utilisateurSession): ?>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge-role"><?= e(strtoupper($roleSession)) ?></span>
                    <a href="<?= APP_BASE_URL ?>/public/deconnexion.php" class="btn btn-outline-danger btn-arrondi px-4">Déconnexion</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container pb-4">