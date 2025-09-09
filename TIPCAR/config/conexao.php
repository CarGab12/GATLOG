<?php

class Conexao
{
    public static $host = 'localhost';
    public static $dbname = 'tipcar';
    public static $user = 'root';
    public static $password = '';

    public static function conectar()
    {
        try {
            $pdo = new PDO(
                "mysql:host=" . self::$host . ";dbname=" . self::$dbname,
                self::$user,
                self::$password
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (Exception $e) {
            echo 'Erro de conexao: ' . $e->getMessage();
        }
    }
}
