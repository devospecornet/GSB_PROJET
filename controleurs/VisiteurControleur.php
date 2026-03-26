<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../modeles/FicheRepository.php';
require_once __DIR__ . '/../modeles/JustificatifRepository.php';
require_once __DIR__ . '/../modeles/HorsForfaitRepository.php';

class VisiteurControleur
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

        $message = '';
        $typeMessage = 'info';

        $idUtilisateur = (int)$_SESSION['utilisateur']['id'];
        $filtreMois = trim($_GET['mois'] ?? '');
        $filtreStatut = trim($_GET['statut'] ?? '');

        $ficheEnModification = null;
        $valeursFormulaire = [
            'id' => '',
            'mois' => '',
            'frais_essence' => '',
            'frais_hotel' => '',
            'frais_resto' => '',
        ];

        if (isset($_GET['modifier'])) {
            $idFiche = (int)$_GET['modifier'];
            $ficheEnModification = $this->ficheRepository->trouverParIdEtUtilisateur($idFiche, $idUtilisateur);

            if (!$ficheEnModification || !in_array($ficheEnModification['statut'], ['saisie', 'refusee'], true)) {
                $ficheEnModification = null;
            } else {
                $montants = $this->extraireMontantsDepuisCommentaire((string)$ficheEnModification['commentaire_visiteur']);

                $valeursFormulaire = [
                    'id' => $ficheEnModification['id'],
                    'mois' => $ficheEnModification['mois'],
                    'frais_essence' => $montants['essence'] > 0 ? (string)$montants['essence'] : '',
                    'frais_hotel' => $montants['hotel'] > 0 ? (string)$montants['hotel'] : '',
                    'frais_resto' => $montants['resto'] > 0 ? (string)$montants['resto'] : '',
                ];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['enregistrer_fiche'])) {
                $mois = trim($_POST['mois'] ?? '');
                $fraisEssence = max(0, (float)($_POST['frais_essence'] ?? 0));
                $fraisHotel = max(0, (float)($_POST['frais_hotel'] ?? 0));
                $fraisResto = max(0, (float)($_POST['frais_resto'] ?? 0));

                $valeursFormulaire = [
                    'id' => $_POST['id_fiche'] ?? '',
                    'mois' => $mois,
                    'frais_essence' => ($_POST['frais_essence'] ?? '') !== '' ? (string)$_POST['frais_essence'] : '',
                    'frais_hotel' => ($_POST['frais_hotel'] ?? '') !== '' ? (string)$_POST['frais_hotel'] : '',
                    'frais_resto' => ($_POST['frais_resto'] ?? '') !== '' ? (string)$_POST['frais_resto'] : '',
                ];

                $montantTotal = $fraisEssence + $fraisHotel + $fraisResto;
                $commentaireVisiteur = "Essence : {$fraisEssence} € | Hôtel : {$fraisHotel} € | Resto : {$fraisResto} €";

                if ($mois === '' || !preg_match('/^\d{4}-\d{2}$/', $mois)) {
                    $message = 'Veuillez renseigner un mois et une année valides.';
                    $typeMessage = 'danger';
                } else {
                    $idFiche = (int)($_POST['id_fiche'] ?? 0);

                    if ($idFiche > 0) {
                        if ($this->ficheRepository->ficheDuMoisExiste($idUtilisateur, $mois, $idFiche)) {
                            $message = 'Une autre fiche existe déjà pour ce mois.';
                            $typeMessage = 'warning';
                        } elseif ($this->ficheRepository->modifier($idFiche, $idUtilisateur, $mois, $montantTotal, $commentaireVisiteur)) {
                            $this->ficheRepository->recalculerMontantTotal($idFiche);
                            $message = 'La fiche a été modifiée.';
                            $typeMessage = 'success';
                        } else {
                            $message = 'Impossible de modifier cette fiche.';
                            $typeMessage = 'danger';
                        }
                    } else {
                        if ($this->ficheRepository->ficheDuMoisExiste($idUtilisateur, $mois)) {
                            $message = 'Une fiche existe déjà pour ce mois.';
                            $typeMessage = 'warning';
                        } elseif ($this->ficheRepository->creer($idUtilisateur, $mois, $montantTotal, $commentaireVisiteur)) {
                            $message = 'Brouillon enregistré avec succès.';
                            $typeMessage = 'success';
                            $valeursFormulaire = [
                                'id' => '',
                                'mois' => '',
                                'frais_essence' => '',
                                'frais_hotel' => '',
                                'frais_resto' => '',
                            ];
                        } else {
                            $message = 'Erreur lors de la création.';
                            $typeMessage = 'danger';
                        }
                    }
                }
            }

            if (isset($_POST['transmettre_fiche'])) {
                $idFiche = (int)($_POST['id_fiche'] ?? 0);
                $ficheCourante = $this->ficheRepository->trouverParIdEtUtilisateur($idFiche, $idUtilisateur);
                $nombreJustificatifs = $this->justificatifRepository->compterParFiche($idFiche);
                $nombreHorsForfaits = $this->horsForfaitRepository->compterParFiche($idFiche);

                if (!$ficheCourante) {
                    $message = 'Fiche introuvable.';
                    $typeMessage = 'danger';
                } elseif ($nombreJustificatifs <= 0) {
                    $message = 'Vous devez ajouter au moins un justificatif avant de transmettre la fiche.';
                    $typeMessage = 'warning';
                } elseif ($nombreHorsForfaits > 0 && !$this->justificatifRepository->existeAvecTva($idFiche)) {
                    $message = 'Impossible de transmettre : une fiche avec hors forfait doit comporter au moins un justificatif contenant la TVA.';
                    $typeMessage = 'warning';
                } elseif ($this->ficheRepository->transmettre($idFiche, $idUtilisateur)) {
                    $message = 'La fiche a été transmise.';
                    $typeMessage = 'success';
                } else {
                    $message = 'Transmission impossible.';
                    $typeMessage = 'danger';
                }
            }

            if (isset($_POST['supprimer_fiche'])) {
                $idFiche = (int)($_POST['id_fiche'] ?? 0);

                if ($this->ficheRepository->supprimer($idFiche, $idUtilisateur)) {
                    $message = 'La fiche a été supprimée.';
                    $typeMessage = 'success';
                } else {
                    $message = 'Suppression impossible.';
                    $typeMessage = 'danger';
                }
            }
        }

        $fiches = $this->ficheRepository->getFichesVisiteur(
            $idUtilisateur,
            $filtreMois !== '' ? $filtreMois : null,
            $filtreStatut !== '' ? $filtreStatut : null
        );

        require __DIR__ . '/../vues/visiteur/liste.php';
    }

    public function afficherSynthese(): void
    {
        $this->verifierAcces();

        $message = '';
        $typeMessage = 'info';

        $idUtilisateur = (int)$_SESSION['utilisateur']['id'];
        $idFiche = (int)($_GET['id'] ?? $_POST['id_fiche'] ?? 0);

        if ($idFiche <= 0) {
            header('Location: ' . APP_BASE_URL . '/public/visiteur.php');
            exit;
        }

        $fiche = $this->ficheRepository->trouverParId($idFiche);

        if (!$fiche || (int)$fiche['id_utilisateur'] !== $idUtilisateur) {
            header('Location: ' . APP_BASE_URL . '/public/visiteur.php');
            exit;
        }

        $horsForfaitEnEdition = null;
        $typesAutorises = ['petit_dejeuner', 'repas_midi', 'repas_soir', 'nuitee'];

        if (isset($_GET['edit_hf']) && in_array($fiche['statut'], ['saisie', 'refusee'], true)) {
            $idHorsForfaitEdition = (int)$_GET['edit_hf'];
            $horsForfaitEnEdition = $this->horsForfaitRepository->trouverParIdEtFiche($idHorsForfaitEdition, $idFiche);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['ajouter_justificatif']) && in_array($fiche['statut'], ['saisie', 'refusee'], true)) {
                if (isset($_FILES['justificatif']) && $_FILES['justificatif']['error'] === UPLOAD_ERR_OK) {
                    $nomReel = $_FILES['justificatif']['name'];
                    $extension = strtolower(pathinfo($nomReel, PATHINFO_EXTENSION));
                    $extensionsAutorisees = ['pdf', 'jpg', 'jpeg', 'png'];
                    $contientTva = isset($_POST['contient_tva']) && $_POST['contient_tva'] === '1';

                    if (in_array($extension, $extensionsAutorisees, true)) {
                        $nomServeur = uniqid('justif_', true) . '.' . $extension;
                        $dossier = __DIR__ . '/../stockage/';

                        if (!is_dir($dossier)) {
                            mkdir($dossier, 0777, true);
                        }

                        $destination = $dossier . $nomServeur;

                        if (move_uploaded_file($_FILES['justificatif']['tmp_name'], $destination)) {
                            $this->justificatifRepository->ajouter($idFiche, $nomReel, $nomServeur, $extension, $contientTva);
                            $message = 'Justificatif ajouté.';
                            $typeMessage = 'success';
                        } else {
                            $message = 'Erreur lors de l’envoi du fichier.';
                            $typeMessage = 'danger';
                        }
                    } else {
                        $message = 'Format non autorisé. Utilisez PDF, JPG, JPEG ou PNG.';
                        $typeMessage = 'warning';
                    }
                }
            }

            if (isset($_POST['supprimer_justificatif']) && in_array($fiche['statut'], ['saisie', 'refusee'], true)) {
                $idJustificatif = (int)($_POST['id_justificatif'] ?? 0);
                $justificatifsActuels = $this->justificatifRepository->getParFiche($idFiche);

                foreach ($justificatifsActuels as $justificatif) {
                    if ((int)$justificatif['id'] === $idJustificatif) {
                        $chemin = __DIR__ . '/../stockage/' . $justificatif['nom_serveur'];

                        if (is_file($chemin)) {
                            unlink($chemin);
                        }

                        $this->justificatifRepository->supprimer($idJustificatif, $idFiche);
                        $message = 'Justificatif supprimé.';
                        $typeMessage = 'success';
                        break;
                    }
                }
            }

            if ((isset($_POST['ajouter_hors_forfait']) || isset($_POST['modifier_hors_forfait'])) && in_array($fiche['statut'], ['saisie', 'refusee'], true)) {
                $typeConsommation = trim($_POST['type_consommation'] ?? '');
                $montant = ($_POST['montant_hors_forfait'] ?? '') === '' ? 0.0 : max(0, (float)($_POST['montant_hors_forfait'] ?? 0));
                $commentaire = trim($_POST['commentaire_hors_forfait'] ?? '');
                $tauxTva = ($_POST['taux_tva'] ?? '') === '' ? 0.0 : (float)($_POST['taux_tva'] ?? 0);
                $idHorsForfait = (int)($_POST['id_hors_forfait'] ?? 0);

                if (!in_array($typeConsommation, $typesAutorises, true)) {
                    $message = 'Type de hors forfait invalide.';
                    $typeMessage = 'warning';
                } elseif ($commentaire === '') {
                    $message = 'Le commentaire est obligatoire pour un hors forfait.';
                    $typeMessage = 'warning';
                } elseif ($montant <= 0) {
                    $message = 'Le montant TTC doit être supérieur à 0.';
                    $typeMessage = 'warning';
                } elseif (!$this->horsForfaitRepository->tauxTvaAutorise($tauxTva)) {
                    $message = 'Le taux de TVA doit être 5 %, 10 % ou 20 %.';
                    $typeMessage = 'warning';
                } elseif (!$this->justificatifRepository->existeAvecTva($idFiche)) {
                    $message = 'Ajoutez d’abord un justificatif contenant la TVA avant de gérer les hors forfaits.';
                    $typeMessage = 'warning';
                } else {
                    $montantMaximum = $this->horsForfaitRepository->montantMaximum($typeConsommation);

                    if ($montant > $montantMaximum) {
                        $message = 'Le montant dépasse le plafond autorisé pour cette consommation.';
                        $typeMessage = 'danger';
                    } elseif (isset($_POST['ajouter_hors_forfait'])) {
                        if ($this->horsForfaitRepository->ajouter($idFiche, $typeConsommation, $montant, $commentaire, $tauxTva)) {
                            $this->ficheRepository->recalculerMontantTotal($idFiche);
                            $message = 'Hors forfait ajouté avec succès.';
                            $typeMessage = 'success';
                        } else {
                            $message = 'Erreur lors de l’ajout du hors forfait.';
                            $typeMessage = 'danger';
                        }
                    } else {
                        $horsForfaitExistant = $this->horsForfaitRepository->trouverParIdEtFiche($idHorsForfait, $idFiche);

                        if (!$horsForfaitExistant) {
                            $message = 'Hors forfait introuvable.';
                            $typeMessage = 'danger';
                        } elseif ($this->horsForfaitRepository->modifier($idHorsForfait, $idFiche, $typeConsommation, $montant, $commentaire, $tauxTva)) {
                            $this->ficheRepository->recalculerMontantTotal($idFiche);
                            $message = 'Hors forfait modifié avec succès.';
                            $typeMessage = 'success';
                            $horsForfaitEnEdition = null;
                        } else {
                            $message = 'Modification du hors forfait impossible.';
                            $typeMessage = 'danger';
                        }
                    }
                }
            }

            if (isset($_POST['supprimer_hors_forfait']) && in_array($fiche['statut'], ['saisie', 'refusee'], true)) {
                $idHorsForfait = (int)($_POST['id_hors_forfait'] ?? 0);

                if ($this->horsForfaitRepository->supprimer($idHorsForfait, $idFiche)) {
                    $this->ficheRepository->recalculerMontantTotal($idFiche);
                    $message = 'Hors forfait supprimé.';
                    $typeMessage = 'success';
                    $horsForfaitEnEdition = null;
                } else {
                    $message = 'Suppression du hors forfait impossible.';
                    $typeMessage = 'danger';
                }
            }

            if (isset($_POST['annuler_edition_hors_forfait'])) {
                $horsForfaitEnEdition = null;
            }
        }

        $fiche = $this->ficheRepository->trouverParId($idFiche);
        $justificatifs = $this->justificatifRepository->getParFiche($idFiche);
        $horsForfaits = $this->horsForfaitRepository->getParFiche($idFiche);

        $montantHorsForfait = 0.0;
        foreach ($horsForfaits as $horsForfait) {
            $montantHorsForfait += (float)($horsForfait['montant_ttc'] ?? 0);
        }

        $montantForfait = (float)$fiche['montant_total'] - $montantHorsForfait;
        if ($montantForfait < 0) {
            $montantForfait = 0.0;
        }

        $libellesHorsForfait = [
            'petit_dejeuner' => 'Petit déjeuner',
            'repas_midi' => 'Repas du midi',
            'repas_soir' => 'Repas du soir',
            'nuitee' => 'Nuitée',
        ];

        require __DIR__ . '/../vues/visiteur/synthese.php';
    }

    private function verifierAcces(): void
    {
        exigerConnexion(['visiteur']);
    }

    private function extraireMontantsDepuisCommentaire(string $commentaire): array
    {
        $resultat = [
            'essence' => 0.0,
            'hotel' => 0.0,
            'resto' => 0.0,
        ];

        if (preg_match('/Essence\s*:\s*([0-9]+(?:[.,][0-9]+)?)\s*€/u', $commentaire, $m)) {
            $resultat['essence'] = (float)str_replace(',', '.', $m[1]);
        }

        if (preg_match('/Hôtel\s*:\s*([0-9]+(?:[.,][0-9]+)?)\s*€/u', $commentaire, $m)) {
            $resultat['hotel'] = (float)str_replace(',', '.', $m[1]);
        }

        if (preg_match('/Resto\s*:\s*([0-9]+(?:[.,][0-9]+)?)\s*€/u', $commentaire, $m)) {
            $resultat['resto'] = (float)str_replace(',', '.', $m[1]);
        }

        return $resultat;
    }
}
