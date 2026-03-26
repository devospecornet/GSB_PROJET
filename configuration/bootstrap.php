<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../noyau/securite.php';

if (!defined('APP_BASE_URL')) {
    define('APP_BASE_URL', '/BTS_SIO2/GSB_Frais_POO');
}

if (!function_exists('e')) {
    function e(?string $valeur): string
    {
        return htmlspecialchars((string)$valeur, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formaterMoisAnnee')) {
    function formaterMoisAnnee(?string $valeur): string
    {
        if (!$valeur) {
            return '';
        }

        if (preg_match('/^\d{4}-\d{2}$/', $valeur)) {
            $date = DateTime::createFromFormat('Y-m', $valeur);

            if ($date) {
                $mois = [
                    'January' => 'janvier',
                    'February' => 'février',
                    'March' => 'mars',
                    'April' => 'avril',
                    'May' => 'mai',
                    'June' => 'juin',
                    'July' => 'juillet',
                    'August' => 'août',
                    'September' => 'septembre',
                    'October' => 'octobre',
                    'November' => 'novembre',
                    'December' => 'décembre',
                ];

                $nomMoisAnglais = $date->format('F');
                $nomMoisFrancais = $mois[$nomMoisAnglais] ?? $nomMoisAnglais;

                return ucfirst($nomMoisFrancais) . ' ' . $date->format('Y');
            }
        }

        return $valeur;
    }
}
