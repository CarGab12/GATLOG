<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';
$pdo = Conexao::conectar();
include('../includes/audit.php');

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success'=>false,'msg'=>'ID inválido']);
    exit;
}

$stmt_old = $pdo->prepare("SELECT * FROM cadastros WHERE id=?");
$stmt_old->execute([$id]);
$old_data = $stmt_old->fetch(PDO::FETCH_ASSOC);

try {
    $stmt = $pdo->prepare("DELETE FROM cadastros WHERE id = ?");
    $stmt->execute([$id]);

    log_audit($pdo, 'cadastros', $id, 'delete', $old_data, null);

    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'msg'=>$e->getMessage()]);
}
?>
