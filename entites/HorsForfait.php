<?php

class HorsForfait
{
    private int $id;
    private int $idFiche;
    private string $typeConsommation;
    private float $montant;
    private string $commentaire;
    private string $dateAjout;
    private float $tauxTva;
    private float $montantHt;
    private float $montantTva;
    private float $montantTtc;

    public function __construct(array $donnees)
    {
        $this->id = (int)($donnees['id'] ?? 0);
        $this->idFiche = (int)($donnees['id_fiche'] ?? 0);
        $this->typeConsommation = (string)($donnees['type_consommation'] ?? '');
        $this->montant = (float)($donnees['montant'] ?? 0);
        $this->commentaire = (string)($donnees['commentaire'] ?? '');
        $this->dateAjout = (string)($donnees['date_ajout'] ?? '');
        $this->tauxTva = (float)($donnees['taux_tva'] ?? 20.00);
        $this->montantHt = (float)($donnees['montant_ht'] ?? 0);
        $this->montantTva = (float)($donnees['montant_tva'] ?? 0);
        $this->montantTtc = (float)($donnees['montant_ttc'] ?? $this->montant);
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getIdFiche(): int
    {
        return $this->idFiche;
    }
    public function getTypeConsommation(): string
    {
        return $this->typeConsommation;
    }
    public function getMontant(): float
    {
        return $this->montant;
    }
    public function getCommentaire(): string
    {
        return $this->commentaire;
    }
    public function getDateAjout(): string
    {
        return $this->dateAjout;
    }
    public function getTauxTva(): float
    {
        return $this->tauxTva;
    }
    public function getMontantHt(): float
    {
        return $this->montantHt;
    }
    public function getMontantTva(): float
    {
        return $this->montantTva;
    }
    public function getMontantTtc(): float
    {
        return $this->montantTtc;
    }
}
