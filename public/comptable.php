<?php
session_start();

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../controleurs/ComptableControleur.php';

$controleur = new ComptableControleur();
$controleur->afficherListe();
