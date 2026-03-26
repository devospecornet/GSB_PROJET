<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../entites/Justificatif.php';

class JustificatifRepository
{
    private PDO $bdd;

    public function __construct()
    {
        $this->bdd = Database::getConnexion();
    }

    public function getParFiche(int $idFiche): array
    {
        $sql = $this->bdd->prepare('
            SELECT *
            FROM justificatifs
            WHERE id_fiche = ?
            ORDER BY date_envoi DESC, id DESC
        ');
        $sql->execute([$idFiche]);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouter(
        int $idFiche,
        string $nomReel,
        string $nomServeur,
        string $extension,
        bool $contientTva = false
    ): bool {
        $sql = $this->bdd->prepare('
            INSERT INTO justificatifs (id_fiche, nom_reel, nom_serveur, extension, contient_tva)
            VALUES (?, ?, ?, ?, ?)
        ');

        return $sql->execute([
            $idFiche,
            $nomReel,
            $nomServeur,
            $extension,
            $contientTva ? 1 : 0
        ]);
    }

    public function supprimer(int $idJustificatif, int $idFiche): bool
    {
        $sql = $this->bdd->prepare('
            DELETE FROM justificatifs
            WHERE id = ? AND id_fiche = ?
        ');
        $sql->execute([$idJustificatif, $idFiche]);

        return $sql->rowCount() > 0;
    }

    public function compterParFiche(int $idFiche): int
    {
        $sql = $this->bdd->prepare('SELECT COUNT(*) AS total FROM justificatifs WHERE id_fiche = ?');
        $sql->execute([$idFiche]);
        $resultat = $sql->fetch(PDO::FETCH_ASSOC);

        return (int)($resultat['total'] ?? 0);
    }

    public function existeAvecTva(int $idFiche): bool
    {
        $sql = $this->bdd->prepare('
            SELECT id
            FROM justificatifs
            WHERE id_fiche = ? AND contient_tva = 1
            LIMIT 1
        ');
        $sql->execute([$idFiche]);

        return (bool)$sql->fetch();
    }
}
