<?php
session_start();

require_once __DIR__ . '/../configuration/bootstrap.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: ' . APP_BASE_URL . '/public/index.php');
    exit;
}

$role = $_SESSION['utilisateur']['role'];

if ($role === 'visiteur') {
    require_once __DIR__ . '/../controleurs/VisiteurControleur.php';
    $controleur = new VisiteurControleur();
    $controleur->afficherSynthese();
    exit;
}

require_once __DIR__ . '/../controleurs/ComptableControleur.php';
$controleur = new ComptableControleur();
$controleur->afficherDetail();
