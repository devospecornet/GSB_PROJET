<?php

class Utilisateur
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $role;

    public function __construct(array $donnees)
    {
        $this->id = (int)$donnees['id'];
        $this->nom = $donnees['nom'];
        $this->prenom = $donnees['prenom'];
        $this->email = $donnees['email'];
        $this->role = $donnees['role'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
