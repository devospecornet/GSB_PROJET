<?php

class Justificatif
{
    private int $id;
    private int $idFiche;
    private string $nomReel;
    private string $nomServeur;
    private string $extension;
    private string $dateEnvoi;

    public function __construct(array $donnees)
    {
        $this->id = (int)$donnees['id'];
        $this->idFiche = (int)$donnees['id_fiche'];
        $this->nomReel = $donnees['nom_reel'];
        $this->nomServeur = $donnees['nom_serveur'];
        $this->extension = $donnees['extension'];
        $this->dateEnvoi = $donnees['date_envoi'];
    }
}
