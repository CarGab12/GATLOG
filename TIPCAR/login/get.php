<?php
include('../config/conexao.php');

$pdo = Conexao::conectar();
session_start();
$sql = "SELECT id, email, senha FROM usuarios";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($usuarios);
echo "</pre>";
?>