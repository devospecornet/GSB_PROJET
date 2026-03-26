<?php
session_start();

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../controleurs/AuthControleur.php';

$controleur = new AuthControleur();
$controleur->afficherPageConnexion();
