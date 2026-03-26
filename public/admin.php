<?php
session_start();

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../controleurs/AdminControleur.php';

$controleur = new AdminControleur();
$controleur->afficherUtilisateurs();
