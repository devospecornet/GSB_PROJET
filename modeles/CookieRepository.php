<?php

require_once __DIR__ . '/../configuration/bootstrap.php';

class CookieRepository
{
    private PDO $bdd;

    public function __construct()
    {
        $this->bdd = Database::getConnexion();
    }

    public function mettreAJourConsentement(int $idUtilisateur, int $consentement): bool
    {
        $sql = $this->bdd->prepare('
            UPDATE utilisateurs
            SET consentement_cookies = ?
            WHERE id = ?
        ');

        return $sql->execute([$consentement, $idUtilisateur]);
    }
}
