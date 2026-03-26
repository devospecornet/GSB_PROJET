<?php

class Database
{
    private static ?PDO $instance = null;

    public static function getConnexion(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new PDO(
                'mysql:host=localhost;dbname=gsb_frais_poo;charset=utf8mb4',
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$instance;
    }
}
