<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - GSB Frais POO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #eef2f7;
        }

        .carte-auth {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .panneau-gauche {
            background: linear-gradient(135deg, #1769ff, #3d8bfd);
            color: white;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-lg-10">
                <div class="card carte-auth">
                    <div class="row g-0">
                        <div class="col-lg-5 panneau-gauche p-5 d-flex flex-column justify-content-center">
                            <h1 class="display-6 fw-bold">GSB Frais</h1>
                            <p class="mb-0">
                                Nouvelle version orientée objet de l’application de gestion des notes de frais.
                            </p>
                        </div>

                        <div class="col-lg-7 p-4 p-md-5 bg-white">
                            <h2 class="fw-bold mb-4">Connexion</h2>

                            <?php if (!empty($message)): ?>
                                <div class="alert alert-<?= e($typeMessage) ?>">
                                    <?= e($message) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Adresse e-mail</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Mot de passe</label>
                                    <input type="password" name="mot_de_passe" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                            </form>

                            <hr class="my-4">

                            <div class="small text-secondary">
                                Comptes de démonstration :
                                <br>admin@gsb.local / Azerty
                                <br>comptable@gsb.local / Azerty
                                <br>visiteur@gsb.local / Azerty
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>