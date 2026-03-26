<?php

class FicheFrais
{
    private int $id;
    private int $idUtilisateur;
    private string $mois;
    private float $montantTotal;
    private string $statut;
    private ?string $commentaireVisiteur;
    private ?string $commentaireComptable;
    private string $dateCreation;
    private string $dateModification;

    public function __construct(array $donnees)
    {
        $this->id = (int)$donnees['id'];
        $this->idUtilisateur = (int)$donnees['id_utilisateur'];
        $this->mois = $donnees['mois'];
        $this->montantTotal = (float)$donnees['montant_total'];
        $this->statut = $donnees['statut'];
        $this->commentaireVisiteur = $donnees['commentaire_visiteur'] ?? null;
        $this->commentaireComptable = $donnees['commentaire_comptable'] ?? null;
        $this->dateCreation = $donnees['date_creation'];
        $this->dateModification = $donnees['date_modification'];
    }

    public function estModifiable(): bool
    {
        return in_array($this->statut, ['saisie', 'refusee'], true);
    }
}
