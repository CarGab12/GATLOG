<?php

session_start();
require_once '../config/database.php';
require_once '../includes/audit.php';

$pdo = Conexao::conectar();

$id = $_POST['id'] ?? null;
$statusNovo = $_POST['status'] ?? null;

if (!$id || !$statusNovo) {
    echo json_encode(['success' => false, 'message' => 'ID ou status inválido']);
    exit;
}


$stmt = $pdo->prepare("SELECT status FROM cadastros WHERE id = :id");
$stmt->execute([':id' => $id]);
$antigo = $stmt->fetch(PDO::FETCH_ASSOC);
$statusAntigo = $antigo['status'] ?? null;


$stmt = $pdo->prepare("UPDATE cadastros SET status = :status WHERE id = :id");
$success = $stmt->execute([':status' => $statusNovo, ':id' => $id]);

if ($success) {

    log_audit($pdo, 'cadastros', $id, 'status', ['status' => $statusAntigo], ['status' => $statusNovo]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
}
