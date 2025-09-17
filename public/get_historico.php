<?php
include('../config/database.php');
$pdo = Conexao::conectar();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$tabela = isset($_GET['tabela']) ? $_GET['tabela'] : '';

if ($id <= 0 || empty($tabela)) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT al.*, 
               al.user_name AS usuario, 
               DATE_FORMAT(al.created_at, '%d/%m/%Y %H:%i:%s') AS data_hora
        FROM audit_logs al
        WHERE al.table_name = :tabela AND al.record_id = :id
        ORDER BY al.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':tabela' => $tabela,
    ':id' => $id
]);

$historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($historico as &$h) {
    $diffs = [];

    $old = !empty($h['old_data']) ? json_decode($h['old_data'], true) : [];
    $new = !empty($h['new_data']) ? json_decode($h['new_data'], true) : [];

    foreach ($new as $campo => $valorNovo) {
        $valorAntigo = $old[$campo] ?? null;

        if (is_numeric($valorAntigo) && is_numeric($valorNovo)) {
            $valorAntigoNorm = (float) $valorAntigo;
            $valorNovoNorm = (float) $valorNovo;
        } else {
            $valorAntigoNorm = is_string($valorAntigo) ? trim($valorAntigo) : $valorAntigo;
            $valorNovoNorm = is_string($valorNovo) ? trim($valorNovo) : $valorNovo;
        }

        if ($valorNovoNorm !== $valorAntigoNorm) {
            $diffs[$campo] = [
                'de' => $valorAntigo,
                'para' => $valorNovo
            ];
        }
    }


    $h['alteracoes'] = $diffs;

    unset($h['old_data'], $h['new_data']);
}

header('Content-Type: application/json');
ob_clean();
echo json_encode($historico, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
