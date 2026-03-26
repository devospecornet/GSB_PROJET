<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../entites/FicheFrais.php';

class FicheRepository
{
    private PDO $bdd;

    public function __construct()
    {
        $this->bdd = Database::getConnexion();
    }

    public function creer(int $idUtilisateur, string $mois, float $montantTotal, string $commentaireVisiteur): bool
    {
        $this->bdd->beginTransaction();

        try {
            $sql = $this->bdd->prepare('
                INSERT INTO fiches_frais (id_utilisateur, mois, montant_total, statut, commentaire_visiteur)
                VALUES (?, ?, ?, "saisie", ?)
            ');
            $sql->execute([$idUtilisateur, $mois, $montantTotal, $commentaireVisiteur]);

            $idFiche = (int)$this->bdd->lastInsertId();
            $numeroFiche = 'FF-' . str_pad((string)$idFiche, 6, '0', STR_PAD_LEFT);

            $sqlNumero = $this->bdd->prepare('
                UPDATE fiches_frais
                SET numero_fiche = ?
                WHERE id = ?
            ');
            $sqlNumero->execute([$numeroFiche, $idFiche]);

            $this->bdd->commit();
            return true;
        } catch (Throwable $e) {
            $this->bdd->rollBack();
            return false;
        }
    }

    public function modifier(int $idFiche, int $idUtilisateur, string $mois, float $montantTotal, string $commentaireVisiteur): bool
    {
        $sql = $this->bdd->prepare('
            UPDATE fiches_frais
            SET mois = ?, montant_total = ?, commentaire_visiteur = ?, statut = "saisie"
            WHERE id = ? AND id_utilisateur = ? AND (statut = "saisie" OR statut = "refusee")
        ');
        $sql->execute([$mois, $montantTotal, $commentaireVisiteur, $idFiche, $idUtilisateur]);

        return $sql->rowCount() > 0;
    }

    public function transmettre(int $idFiche, int $idUtilisateur): bool
    {
        $sql = $this->bdd->prepare('
            UPDATE fiches_frais
            SET statut = "transmise"
            WHERE id = ? AND id_utilisateur = ? AND (statut = "saisie" OR statut = "refusee")
        ');
        $sql->execute([$idFiche, $idUtilisateur]);

        return $sql->rowCount() > 0;
    }

    public function supprimer(int $idFiche, int $idUtilisateur): bool
    {
        $sql = $this->bdd->prepare('
            DELETE FROM fiches_frais
            WHERE id = ? AND id_utilisateur = ? AND (statut = "saisie" OR statut = "refusee")
        ');
        $sql->execute([$idFiche, $idUtilisateur]);

        return $sql->rowCount() > 0;
    }

    public function trouverParId(int $id): ?array
    {
        $sql = $this->bdd->prepare('
            SELECT f.*, u.nom, u.prenom, u.email
            FROM fiches_frais f
            INNER JOIN utilisateurs u ON u.id = f.id_utilisateur
            WHERE f.id = ?
            LIMIT 1
        ');
        $sql->execute([$id]);

        $resultat = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    public function trouverParIdEtUtilisateur(int $idFiche, int $idUtilisateur): ?array
    {
        $sql = $this->bdd->prepare('
            SELECT *
            FROM fiches_frais
            WHERE id = ? AND id_utilisateur = ?
            LIMIT 1
        ');
        $sql->execute([$idFiche, $idUtilisateur]);

        $resultat = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    public function ficheDuMoisExiste(int $idUtilisateur, string $mois, ?int $idExclu = null): bool
    {
        if ($idExclu !== null) {
            $sql = $this->bdd->prepare('
                SELECT id
                FROM fiches_frais
                WHERE id_utilisateur = ? AND mois = ? AND id <> ?
                LIMIT 1
            ');
            $sql->execute([$idUtilisateur, $mois, $idExclu]);
        } else {
            $sql = $this->bdd->prepare('
                SELECT id
                FROM fiches_frais
                WHERE id_utilisateur = ? AND mois = ?
                LIMIT 1
            ');
            $sql->execute([$idUtilisateur, $mois]);
        }

        return (bool)$sql->fetch();
    }

    public function getFichesVisiteur(int $idUtilisateur, ?string $mois = null, ?string $statut = null): array
    {
        $requete = '
            SELECT *
            FROM fiches_frais
            WHERE id_utilisateur = ?
        ';
        $parametres = [$idUtilisateur];

        if (!empty($mois)) {
            $requete .= ' AND mois = ?';
            $parametres[] = $mois;
        }

        if (!empty($statut)) {
            $requete .= ' AND statut = ?';
            $parametres[] = $statut;
        }

        $requete .= ' ORDER BY mois DESC, id DESC';

        $sql = $this->bdd->prepare($requete);
        $sql->execute($parametres);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFichesComptable(?string $nom = null, ?string $email = null, ?string $mois = null, ?string $statut = null): array
    {
        $requete = '
            SELECT f.*, u.nom, u.prenom, u.email
            FROM fiches_frais f
            INNER JOIN utilisateurs u ON u.id = f.id_utilisateur
            WHERE 1 = 1
        ';
        $parametres = [];

        if (!empty($nom)) {
            $requete .= ' AND (u.nom LIKE ? OR u.prenom LIKE ?)';
            $parametres[] = '%' . $nom . '%';
            $parametres[] = '%' . $nom . '%';
        }

        if (!empty($email)) {
            $requete .= ' AND u.email LIKE ?';
            $parametres[] = '%' . $email . '%';
        }

        if (!empty($mois)) {
            $requete .= ' AND f.mois = ?';
            $parametres[] = $mois;
        }

        if (!empty($statut)) {
            $requete .= ' AND f.statut = ?';
            $parametres[] = $statut;
        }

        $requete .= ' ORDER BY u.nom ASC, u.prenom ASC, f.mois DESC, f.id DESC';

        $sql = $this->bdd->prepare($requete);
        $sql->execute($parametres);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function traiter(int $idFiche, string $statut, string $commentaireComptable): bool
    {
        $sql = $this->bdd->prepare('
            UPDATE fiches_frais
            SET statut = ?, commentaire_comptable = ?
            WHERE id = ? AND statut = "transmise"
        ');
        $sql->execute([$statut, $commentaireComptable, $idFiche]);

        return $sql->rowCount() > 0;
    }

    public function recalculerMontantTotal(int $idFiche): bool
    {
        $fiche = $this->trouverParId($idFiche);

        if (!$fiche) {
            return false;
        }

        $montantForfait = $this->extraireMontantForfaitDepuisCommentaire((string)($fiche['commentaire_visiteur'] ?? ''));

        $sql = $this->bdd->prepare('
            SELECT COALESCE(SUM(montant_ttc), 0) AS total
            FROM hors_forfaits
            WHERE id_fiche = ?
        ');
        $sql->execute([$idFiche]);
        $horsForfait = $sql->fetch(PDO::FETCH_ASSOC);

        $montantTotal = $montantForfait + (float)($horsForfait['total'] ?? 0);

        $update = $this->bdd->prepare('
            UPDATE fiches_frais
            SET montant_total = ?
            WHERE id = ?
        ');
        $update->execute([$montantTotal, $idFiche]);

        return true;
    }

    public function compterToutesLesFiches(): int
    {
        $sql = $this->bdd->query('SELECT COUNT(*) AS total FROM fiches_frais');
        $resultat = $sql->fetch(PDO::FETCH_ASSOC);

        return (int)($resultat['total'] ?? 0);
    }

    public function compterParStatut(string $statut): int
    {
        $sql = $this->bdd->prepare('SELECT COUNT(*) AS total FROM fiches_frais WHERE statut = ?');
        $sql->execute([$statut]);
        $resultat = $sql->fetch(PDO::FETCH_ASSOC);

        return (int)($resultat['total'] ?? 0);
    }

    public function sommeMontantsValides(): float
    {
        $sql = $this->bdd->query('SELECT COALESCE(SUM(montant_total), 0) AS total FROM fiches_frais WHERE statut = "validee"');
        $resultat = $sql->fetch(PDO::FETCH_ASSOC);

        return (float)($resultat['total'] ?? 0);
    }

    public function topUtilisateurs(int $limite = 5): array
    {
        $sql = $this->bdd->prepare('
            SELECT u.nom, u.prenom, u.email, COUNT(f.id) AS nombre_fiches, COALESCE(SUM(f.montant_total), 0) AS total_montants
            FROM utilisateurs u
            LEFT JOIN fiches_frais f ON f.id_utilisateur = u.id
            GROUP BY u.id, u.nom, u.prenom, u.email
            ORDER BY nombre_fiches DESC, total_montants DESC
            LIMIT ?
        ');
        $sql->bindValue(1, $limite, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    private function extraireMontantForfaitDepuisCommentaire(string $commentaire): float
    {
        if ($commentaire === '') {
            return 0.0;
        }

        preg_match_all('/(\d+(?:[.,]\d+)?)\s*€/u', $commentaire, $correspondances);

        if (empty($correspondances[1])) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($correspondances[1] as $valeur) {
            $total += (float)str_replace(',', '.', $valeur);
        }

        return $total;
    }
}
