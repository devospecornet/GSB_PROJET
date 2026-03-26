<?php

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../modeles/ApiRepository.php';
require_once __DIR__ . '/../modeles/UtilisateurRepository.php';
require_once __DIR__ . '/../modeles/FicheRepository.php';
require_once __DIR__ . '/../modeles/HorsForfaitRepository.php';
require_once __DIR__ . '/../modeles/JustificatifRepository.php';

header('Content-Type: application/json; charset=utf-8');

function reponseJson(array $donnees, int $codeHttp = 200): void
{
    http_response_code($codeHttp);
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function lireCorpsJson(): array
{
    $contenu = file_get_contents('php://input');

    if (!$contenu) {
        return [];
    }

    $donnees = json_decode($contenu, true);

    return is_array($donnees) ? $donnees : [];
}

function recupererJetonBearer(): ?string
{
    $enteteAuthorization = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

    if ($enteteAuthorization === '' && function_exists('getallheaders')) {
        $entetes = getallheaders();

        if (isset($entetes['Authorization'])) {
            $enteteAuthorization = $entetes['Authorization'];
        } elseif (isset($entetes['authorization'])) {
            $enteteAuthorization = $entetes['authorization'];
        }
    }

    if (preg_match('/Bearer\s+(.+)/i', $enteteAuthorization, $correspondances)) {
        return trim($correspondances[1]);
    }

    return null;
}

function verifierMethode(array $methodesAutorisees): void
{
    $methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if (!in_array($methode, $methodesAutorisees, true)) {
        reponseJson([
            'succes' => false,
            'message' => 'Méthode non autorisée.'
        ], 405);
    }
}

function utilisateurApiAuthentifie(): array
{
    $apiRepository = new ApiRepository();
    $apiRepository->supprimerJetonsExpires();

    $jeton = recupererJetonBearer();

    if (!$jeton) {
        reponseJson([
            'succes' => false,
            'message' => 'Jeton Bearer manquant.'
        ], 401);
    }

    $ligneJeton = $apiRepository->trouverParJeton($jeton);

    if (!$ligneJeton || strtotime($ligneJeton['date_expiration']) < time()) {
        reponseJson([
            'succes' => false,
            'message' => 'Jeton invalide ou expiré.'
        ], 401);
    }

    if ((int)$ligneJeton['est_approuve'] !== 1) {
        reponseJson([
            'succes' => false,
            'message' => 'Compte non approuvé.'
        ], 403);
    }

    $apiRepository->prolongerJeton($jeton);

    return $ligneJeton;
}
