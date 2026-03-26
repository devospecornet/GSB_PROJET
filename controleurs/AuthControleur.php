<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../modeles/UtilisateurRepository.php';

class AuthControleur
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct()
    {
        $this->utilisateurRepository = new UtilisateurRepository();
    }

    public function afficherPageConnexion(): void
    {
        demarrerSessionSiNecessaire();

        $message = $_SESSION['message_connexion'] ?? '';
        unset($_SESSION['message_connexion']);

        $typeMessage = 'info';

        if (isset($_SESSION['utilisateur'])) {
            verifierSession();
            header('Location: ' . APP_BASE_URL . '/public/tableau_bord.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';

            if ($email === '' || $motDePasse === '') {
                $message = 'Tous les champs sont obligatoires.';
                $typeMessage = 'danger';
            } else {
                $utilisateur = $this->utilisateurRepository->verifierConnexion($email, $motDePasse);

                if (!$utilisateur) {
                    $message = 'Adresse e-mail ou mot de passe incorrect.';
                    $typeMessage = 'danger';
                } else {
                    session_regenerate_id(true);

                    $_SESSION['utilisateur'] = [
                        'id' => (int)$utilisateur['id'],
                        'nom' => $utilisateur['nom'],
                        'prenom' => $utilisateur['prenom'],
                        'email' => $utilisateur['email'],
                        'role' => $utilisateur['role']
                    ];

                    $_SESSION['LAST_ACTIVITY'] = time();

                    header('Location: ' . APP_BASE_URL . '/public/tableau_bord.php');
                    exit;
                }
            }
        }

        require __DIR__ . '/../vues/auth/index.php';
    }
}
