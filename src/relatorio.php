<?php
require '../vendor/autoload.php';
require '../config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo = Conexao::conectar();

$data_inicio = $_POST['data_inicio'] ?? null;
$data_fim = $_POST['data_fim'] ?? null;

if (!$data_inicio || !$data_fim) {
    die('Período inválido');
}

$stmt = $pdo->prepare("SELECT * FROM cadastros WHERE criado_em BETWEEN :inicio AND :fim ORDER BY criado_em DESC");
$stmt->execute([
    ':inicio' => $data_inicio . ' 00:00:00',
    ':fim' => $data_fim . ' 23:59:59'
]);

$cadastros = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Relatório');

$sheet->fromArray(array_keys($cadastros[0] ?? []), NULL, 'A1');

if ($cadastros) {
    $sheet->fromArray($cadastros, NULL, 'A2');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="relatorio.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
