<?php
if (!isset($_SESSION))
    session_start();
require_once '../config/database.php';
include('../includes/audit.php');
$pdo = Conexao::conectar();

if (empty($_SESSION['logado']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';

$sql = "SELECT * FROM cadastros ORDER BY id DESC";
$stmt = $pdo->query($sql);
$cadastros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>