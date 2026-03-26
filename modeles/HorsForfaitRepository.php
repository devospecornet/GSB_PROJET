<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../entites/HorsForfait.php';

class HorsForfaitRepository
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
            FROM hors_forfaits
            WHERE id_fiche = ?
            ORDER BY date_ajout DESC, id DESC
        ');
        $sql->execute([$idFiche]);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParIdEtFiche(int $idHorsForfait, int $idFiche): ?array
    {
        $sql = $this->bdd->prepare('
            SELECT *
            FROM hors_forfaits
            WHERE id = ? AND id_fiche = ?
            LIMIT 1
        ');
        $sql->execute([$idHorsForfait, $idFiche]);

        $resultat = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultat ?: null;
    }

    public function ajouter(
        int $idFiche,
        string $typeConsommation,
        float $montantTtc,
        string $commentaire,
        float $tauxTva
    ): bool {
        $calcul = $this->calculerTva($montantTtc, $tauxTva);
        $libelle = $this->typeLisible($typeConsommation) . ' || ' . trim($commentaire);

        $sql = $this->bdd->prepare('
            INSERT INTO hors_forfaits (
                id_fiche,
                type_consommation,
                date,
                libelle,
                montant,
                commentaire,
                date_ajout,
                taux_tva,
                montant_ht,
                montant_tva,
                montant_ttc
            )
            VALUES (?, ?, CURDATE(), ?, ?, ?, NOW(), ?, ?, ?, ?)
        ');

        return $sql->execute([
            $idFiche,
            $typeConsommation,
            $libelle,
            $montantTtc,
            $commentaire,
            $calcul['taux_tva'],
            $calcul['montant_ht'],
            $calcul['montant_tva'],
            $calcul['montant_ttc']
        ]);
    }

    public function modifier(
        int $idHorsForfait,
        int $idFiche,
        string $typeConsommation,
        float $montantTtc,
        string $commentaire,
        float $tauxTva
    ): bool {
        $calcul = $this->calculerTva($montantTtc, $tauxTva);
        $libelle = $this->typeLisible($typeConsommation) . ' || ' . trim($commentaire);

        $sql = $this->bdd->prepare('
            UPDATE hors_forfaits
            SET
                type_consommation = ?,
                date = CURDATE(),
                libelle = ?,
                montant = ?,
                commentaire = ?,
                date_ajout = NOW(),
                taux_tva = ?,
                montant_ht = ?,
                montant_tva = ?,
                montant_ttc = ?
            WHERE id = ? AND id_fiche = ?
        ');
        $sql->execute([
            $typeConsommation,
            $libelle,
            $montantTtc,
            $commentaire,
            $calcul['taux_tva'],
            $calcul['montant_ht'],
            $calcul['montant_tva'],
            $calcul['montant_ttc'],
            $idHorsForfait,
            $idFiche
        ]);

        return $sql->rowCount() > 0;
    }

    public function supprimer(int $idHorsForfait, int $idFiche): bool
    {
        $sql = $this->bdd->prepare('
            DELETE FROM hors_forfaits
            WHERE id = ? AND id_fiche = ?
        ');
        $sql->execute([$idHorsForfait, $idFiche]);

        return $sql->rowCount() > 0;
    }

    public function compterParFiche(int $idFiche): int
    {
        $sql = $this->bdd->prepare('SELECT COUNT(*) AS total FROM hors_forfaits WHERE id_fiche = ?');
        $sql->execute([$idFiche]);
        $resultat = $sql->fetch(PDO::FETCH_ASSOC);

        return (int)($resultat['total'] ?? 0);
    }

    public function sommeParFiche(int $idFiche): float
    {
        $sql = $this->bdd->prepare('
            SELECT COALESCE(SUM(montant_ttc), 0) AS total
            FROM hors_forfaits
            WHERE id_fiche = ?
        ');
        $sql->execute([$idFiche]);
        $resultat = $sql->fetch(PDO::FETCH_ASSOC);

        return (float)($resultat['total'] ?? 0);
    }

    public function typeLisible(string $type): string
    {
        return match ($type) {
            'petit_dejeuner' => 'Petit déjeuner',
            'repas_midi' => 'Repas du midi',
            'repas_soir' => 'Repas du soir',
            'nuitee' => 'Nuitée',
            default => $type
        };
    }

    public function montantMaximum(string $type): float
    {
        return match ($type) {
            'petit_dejeuner' => 12.00,
            'repas_midi', 'repas_soir' => 23.00,
            'nuitee' => 150.00,
            default => 0.00
        };
    }

    public function tauxTvaAutorise(float $tauxTva): bool
    {
        $valeurs = [5.0, 10.0, 20.0];

        foreach ($valeurs as $valeur) {
            if (abs($tauxTva - $valeur) < 0.001) {
                return true;
            }
        }

        return false;
    }

    public function calculerTva(float $montantTtc, float $tauxTva): array
    {
        $montantTtc = round(max(0, $montantTtc), 2);
        $tauxTva = round(max(0, $tauxTva), 2);

        if (!$this->tauxTvaAutorise($tauxTva)) {
            $tauxTva = 20.00;
        }

        if ($tauxTva <= 0) {
            return [
                'taux_tva' => 0.00,
                'montant_ht' => $montantTtc,
                'montant_tva' => 0.00,
                'montant_ttc' => $montantTtc
            ];
        }

        $montantHt = round($montantTtc / (1 + ($tauxTva / 100)), 2);
        $montantTva = round($montantTtc - $montantHt, 2);

        return [
            'taux_tva' => $tauxTva,
            'montant_ht' => $montantHt,
            'montant_tva' => $montantTva,
            'montant_ttc' => $montantTtc
        ];
    }
}
