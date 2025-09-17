<?php
if (!isset($_SESSION['logado']) || !isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}


$stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$usuario_nome = $usuario['nome'] ?? 'Usuário';
?>