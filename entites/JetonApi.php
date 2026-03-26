<?php

class JetonApi
{
    private int $id;
    private int $idUtilisateur;
    private string $jeton;
    private string $dateCreation;
    private string $dateExpiration;

    public function __construct(array $donnees)
    {
        $this->id = (int)$donnees['id'];
        $this->idUtilisateur = (int)$donnees['id_utilisateur'];
        $this->jeton = $donnees['jeton'];
        $this->dateCreation = $donnees['date_creation'];
        $this->dateExpiration = $donnees['date_expiration'];
    }
}
