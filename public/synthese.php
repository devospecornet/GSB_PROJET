<?php
session_start();

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../controleurs/VisiteurControleur.php';

$controleur = new VisiteurControleur();
$controleur->afficherSynthese();
