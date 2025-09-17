<?php
// Evita redeclaração da classe
if (!class_exists('Conexao')) {
    class Conexao {
        private static $host = 'localhost';
        private static $db   = 'gatlog';
        private static $user = 'root';
        private static $pass = '';
        private static $charset = 'utf8mb4';

        public static function conectar() {
            $dsn = "mysql:host=".self::$host.";dbname=".self::$db.";charset=".self::$charset;
            try {
                $pdo = new PDO($dsn, self::$user, self::$pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch(PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }
    }
}
?>
