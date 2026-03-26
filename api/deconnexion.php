<?php

require_once __DIR__ . '/commun.php';

verifierMethode(['POST']);

$token = recupererJetonBearer();

if (!$token) {
    reponseJson([
        'succes' => false,
        'message' => 'Jeton Bearer manquant.'
    ], 400);
}

$apiRepository = new ApiRepository();
$apiRepository->supprimerJeton($token);

reponseJson([
    'succes' => true,
    'message' => 'Déconnexion API effectuée.'
], 200);
