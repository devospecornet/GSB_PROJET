<?php

require_once __DIR__ . '/../configuration/bootstrap.php';

class UtilisateurRepository
{
    private PDO $bdd;

    public function __construct()
    {
        $this->bdd = Database::getConnexion();
    }

    public function trouverParEmail(string $email): ?array
    {
        $sql = $this->bdd->prepare('
            SELECT *
            FROM utilisateurs
            WHERE email = ?
            LIMIT 1
        ');
        $sql->execute([$email]);

        $resultat = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    public function trouverParId(int $id): ?array
    {
        $sql = $this->bdd->prepare('
            SELECT *
            FROM utilisateurs
            WHERE id = ?
            LIMIT 1
        ');
        $sql->execute([$id]);

        $resultat = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    public function verifierConnexion(string $email, string $motDePasse): ?array
    {
        $utilisateur = $this->trouverParEmail($email);

        if (!$utilisateur) {
            return null;
        }

        if ((int)$utilisateur['est_approuve'] !== 1) {
            return null;
        }

        if (!password_verify($motDePasse, $utilisateur['mdp'])) {
            return null;
        }

        return $utilisateur;
    }

    public function creer(string $nom, string $prenom, string $email, string $motDePasse, string $role): bool
    {
        $motDePasseHash = password_hash($motDePasse, PASSWORD_BCRYPT);

        $sql = $this->bdd->prepare('
            INSERT INTO utilisateurs (nom, prenom, email, mdp, role, est_approuve, consentement_cookies, date_creation)
            VALUES (?, ?, ?, ?, ?, 1, 0, NOW())
        ');

        return $sql->execute([$nom, $prenom, $email, $motDePasseHash, $role]);
    }

    public function supprimer(int $idUtilisateur): bool
    {
        $sql = $this->bdd->prepare('DELETE FROM utilisateurs WHERE id = ?');
        $sql->execute([$idUtilisateur]);

        return $sql->rowCount() > 0;
    }

    public function getTous(): array
    {
        $sql = $this->bdd->query('
            SELECT *
            FROM utilisateurs
            ORDER BY role ASC, nom ASC, prenom ASC
        ');

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
