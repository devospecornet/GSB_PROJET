<?php

require_once __DIR__ . '/../configuration/bootstrap.php';

class ApiRepository
{
    private PDO $bdd;

    public function __construct()
    {
        $this->bdd = Database::getConnexion();
    }

    public function creerJeton(int $idUtilisateur): string
    {
        $jeton = bin2hex(random_bytes(32));

        $sql = $this->bdd->prepare('
            INSERT INTO api_jetons (id_utilisateur, jeton, date_expiration)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))
        ');
        $sql->execute([$idUtilisateur, $jeton]);

        return $jeton;
    }

    public function trouverParJeton(string $jeton): ?array
    {
        $sql = $this->bdd->prepare('
            SELECT
                a.id,
                a.id_utilisateur,
                a.jeton,
                a.date_creation,
                a.date_expiration,
                u.nom,
                u.prenom,
                u.email,
                u.role,
                u.est_approuve
            FROM api_jetons a
            INNER JOIN utilisateurs u ON u.id = a.id_utilisateur
            WHERE a.jeton = ?
            LIMIT 1
        ');
        $sql->execute([$jeton]);

        $resultat = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    public function trouverUtilisateurParJeton(string $jeton): ?array
    {
        $sql = $this->bdd->prepare('
            SELECT u.id, u.nom, u.prenom, u.email, u.role
            FROM api_jetons a
            INNER JOIN utilisateurs u ON u.id = a.id_utilisateur
            WHERE a.jeton = ? AND a.date_expiration >= NOW()
            LIMIT 1
        ');
        $sql->execute([$jeton]);

        $resultat = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    public function prolongerJeton(string $jeton): void
    {
        $sql = $this->bdd->prepare('
            UPDATE api_jetons
            SET date_expiration = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
            WHERE jeton = ?
        ');
        $sql->execute([$jeton]);
    }

    public function supprimerJetonsExpires(): void
    {
        $sql = $this->bdd->prepare('DELETE FROM api_jetons WHERE date_expiration < NOW()');
        $sql->execute();
    }

    public function supprimerJeton(string $jeton): bool
    {
        $sql = $this->bdd->prepare('DELETE FROM api_jetons WHERE jeton = ?');
        $sql->execute([$jeton]);

        return $sql->rowCount() > 0;
    }

    public function supprimerJetonsUtilisateur(int $idUtilisateur): void
    {
        $sql = $this->bdd->prepare('DELETE FROM api_jetons WHERE id_utilisateur = ?');
        $sql->execute([$idUtilisateur]);
    }
}
