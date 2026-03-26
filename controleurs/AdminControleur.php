<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../modeles/UtilisateurRepository.php';
require_once __DIR__ . '/../modeles/FicheRepository.php';

class AdminControleur
{
    private UtilisateurRepository $utilisateurRepository;
    private FicheRepository $ficheRepository;

    public function __construct()
    {
        $this->utilisateurRepository = new UtilisateurRepository();
        $this->ficheRepository = new FicheRepository();
    }

    public function afficherUtilisateurs(): void
    {
        $this->verifierAcces();

        $message = '';
        $typeMessage = 'info';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['creer_utilisateur'])) {
                $nom = trim($_POST['nom'] ?? '');
                $prenom = trim($_POST['prenom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $motDePasse = $_POST['mot_de_passe'] ?? '';
                $role = $_POST['role'] ?? 'visiteur';

                if ($nom === '' || $prenom === '' || $email === '' || $motDePasse === '') {
                    $message = 'Tous les champs sont obligatoires.';
                    $typeMessage = 'danger';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message = 'Adresse e-mail invalide.';
                    $typeMessage = 'warning';
                } elseif (!in_array($role, ['visiteur', 'comptable'], true)) {
                    $message = 'Rôle invalide.';
                    $typeMessage = 'warning';
                } elseif ($this->utilisateurRepository->trouverParEmail($email)) {
                    $message = 'Un utilisateur avec cet e-mail existe déjà.';
                    $typeMessage = 'warning';
                } elseif ($this->utilisateurRepository->creer($nom, $prenom, $email, $motDePasse, $role)) {
                    $message = 'Utilisateur créé avec succès.';
                    $typeMessage = 'success';
                } else {
                    $message = 'Erreur lors de la création.';
                    $typeMessage = 'danger';
                }
            }

            if (isset($_POST['supprimer_utilisateur'])) {
                $idUtilisateur = (int)($_POST['id_utilisateur'] ?? 0);
                $idSession = (int)$_SESSION['utilisateur']['id'];

                if ($idUtilisateur <= 0) {
                    $message = 'Utilisateur invalide.';
                    $typeMessage = 'warning';
                } elseif ($idUtilisateur === $idSession) {
                    $message = 'Vous ne pouvez pas supprimer votre propre compte.';
                    $typeMessage = 'warning';
                } elseif ($this->utilisateurRepository->supprimer($idUtilisateur)) {
                    $message = 'Utilisateur supprimé avec succès.';
                    $typeMessage = 'success';
                } else {
                    $message = 'Suppression impossible.';
                    $typeMessage = 'danger';
                }
            }
        }

        $utilisateurs = $this->utilisateurRepository->getTous();

        require __DIR__ . '/../vues/admin/utilisateurs.php';
    }

    public function afficherToutesLesFiches(): void
    {
        $this->verifierAcces();

        $nom = trim($_GET['nom'] ?? '');
        $email = trim($_GET['email'] ?? '');
        $mois = trim($_GET['mois'] ?? '');
        $statut = trim($_GET['statut'] ?? '');

        $fiches = $this->ficheRepository->getFichesComptable(
            $nom !== '' ? $nom : null,
            $email !== '' ? $email : null,
            $mois !== '' ? $mois : null,
            $statut !== '' ? $statut : null
        );

        $fichesParUtilisateur = [];
        foreach ($fiches as $fiche) {
            $cle = $fiche['nom'] . ' ' . $fiche['prenom'] . '|' . $fiche['email'];
            $fichesParUtilisateur[$cle][] = $fiche;
        }

        $statistiques = [
            'total' => $this->ficheRepository->compterToutesLesFiches(),
            'validees' => $this->ficheRepository->compterParStatut('validee'),
            'refusees' => $this->ficheRepository->compterParStatut('refusee'),
            'transmises' => $this->ficheRepository->compterParStatut('transmise'),
            'saisies' => $this->ficheRepository->compterParStatut('saisie'),
            'montant_valide' => $this->ficheRepository->sommeMontantsValides(),
            'top_utilisateurs' => $this->ficheRepository->topUtilisateurs(5),
        ];

        require __DIR__ . '/../vues/admin/toutes_les_fiches.php';
    }

    private function verifierAcces(): void
    {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'administrateur') {
            header('Location: ' . APP_BASE_URL . '/public/index.php');
            exit;
        }
    }
}
