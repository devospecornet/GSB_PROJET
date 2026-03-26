<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../modeles/FicheRepository.php';
require_once __DIR__ . '/../modeles/JustificatifRepository.php';
require_once __DIR__ . '/../modeles/HorsForfaitRepository.php';

class ComptableControleur
{
    private FicheRepository $ficheRepository;
    private JustificatifRepository $justificatifRepository;
    private HorsForfaitRepository $horsForfaitRepository;

    public function __construct()
    {
        $this->ficheRepository = new FicheRepository();
        $this->justificatifRepository = new JustificatifRepository();
        $this->horsForfaitRepository = new HorsForfaitRepository();
    }

    public function afficherListe(): void
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

        require __DIR__ . '/../vues/comptable/liste.php';
    }

    public function afficherDetail(): void
    {
        $this->verifierAcces();

        $message = '';
        $typeMessage = 'info';

        $idFiche = (int)($_GET['id'] ?? $_POST['id_fiche'] ?? 0);

        if ($idFiche <= 0) {
            header('Location: ' . APP_BASE_URL . '/public/comptable.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['traiter_fiche'])) {
            $statut = $_POST['statut'] ?? '';
            $commentaire = trim($_POST['commentaire_comptable'] ?? '');

            if (in_array($statut, ['validee', 'refusee'], true)) {
                if ($this->ficheRepository->traiter($idFiche, $statut, $commentaire)) {
                    $message = 'La fiche a été traitée.';
                    $typeMessage = 'success';
                } else {
                    $message = 'Traitement impossible.';
                    $typeMessage = 'danger';
                }
            } else {
                $message = 'Statut invalide.';
                $typeMessage = 'warning';
            }
        }

        $fiche = $this->ficheRepository->trouverParId($idFiche);

        if (!$fiche) {
            header('Location: ' . APP_BASE_URL . '/public/comptable.php');
            exit;
        }

        $justificatifs = $this->justificatifRepository->getParFiche($idFiche);
        $horsForfaits = $this->horsForfaitRepository->getParFiche($idFiche);

        $libellesHorsForfait = [
            'petit_dejeuner' => 'Petit déjeuner',
            'repas_midi' => 'Repas du midi',
            'repas_soir' => 'Repas du soir',
            'nuitee' => 'Nuitée',
        ];

        require __DIR__ . '/../vues/comptable/detail.php';
    }

    private function verifierAcces(): void
    {
        exigerConnexion(['comptable', 'administrateur']);
    }
}
