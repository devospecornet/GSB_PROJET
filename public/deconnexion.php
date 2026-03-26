<?php

require_once __DIR__ . '/../configuration/bootstrap.php';

demarrerSessionSiNecessaire();

session_unset();
session_destroy();

header('Location: ' . APP_BASE_URL . '/public/index.php');
exit;
