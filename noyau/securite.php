<?php

if (!function_exists('demarrerSessionSiNecessaire')) {
    function demarrerSessionSiNecessaire(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}

if (!function_exists('verifierSession')) {
    function verifierSession(): void
    {
        demarrerSessionSiNecessaire();

        $timeout = 15 * 60; // 15 minutes

        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - (int)$_SESSION['LAST_ACTIVITY']) > $timeout) {
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['message_connexion'] = 'Votre session a expiré après 15 minutes d\'inactivité. Veuillez vous reconnecter.';
            header('Location: /BTS_SIO2/GSB_Frais_POO/public/index.php');
            exit;
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }
}

if (!function_exists('exigerConnexion')) {
    function exigerConnexion(array $rolesAutorises = []): void
    {
        verifierSession();

        if (!isset($_SESSION['utilisateur'])) {
            header('Location: /BTS_SIO2/GSB_Frais_POO/public/index.php');
            exit;
        }

        if (!empty($rolesAutorises)) {
            $role = $_SESSION['utilisateur']['role'] ?? '';

            if (!in_array($role, $rolesAutorises, true)) {
                header('Location: /BTS_SIO2/GSB_Frais_POO/public/tableau_bord.php');
                exit;
            }
        }
    }
}
