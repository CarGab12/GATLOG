<?php
if (!isset($_SESSION)) session_start();
require_once '../config/database.php';
$pdo = Conexao::conectar();
include('../includes/audit.php');

if (empty($_SESSION['logado']) || empty($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit;
}

function brToFloat($v) {
    if ($v === null || $v === '') return 0;
    $v = str_replace(['.', ' '], '', $v);
    $v = str_replace(',', '.', $v);
    return floatval($v);
}

function brToSqlDate($d) {
    if (!$d) return null;
    $parts = explode('/', $d);
    if (count($parts) === 3) return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    return $d;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    header('Location: ../public/painel.php?msg=erro&detalhe=ID inválido');
    exit;
}

$stmt_old = $pdo->prepare("SELECT * FROM cadastros WHERE id=?");
$stmt_old->execute([$id]);
$old_data = $stmt_old->fetch(PDO::FETCH_ASSOC);

$id_contrato = $_POST['id_contrato'] ?? '';
$data_coleta = brToSqlDate($_POST['data_coleta'] ?? '');
$operacao = $_POST['operacao'] ?? '';
$contratante = $_POST['contratante'] ?? '';
$contato = $_POST['contato'] ?? '';
$tem_motorista = isset($_POST['tem_motorista']) ? 1 : 0;
$motorista_nome = trim($_POST['motorista_nome'] ?? '');
$motorista_cpf = trim($_POST['motorista_cpf'] ?? '');
$tipo_documento = $_POST['tipo_documento'] ?? '';
$documento = $_POST['documento'] ?? '';
$pis = $_POST['pis'] ?? '';
$data_nascimento = brToSqlDate($_POST['data_nascimento'] ?? '');
$tipo_veiculo = $_POST['tipo_veiculo'] ?? '';
$placa_veiculo = $_POST['placa_veiculo'] ?? '';
$eixos = $_POST['eixos'] ?? 0;
$fornecedor = $_POST['fornecedor'] ?? '';
$origem = $_POST['origem'] ?? '';
$destino = $_POST['destino'] ?? '';
$valor_frete = brToFloat($_POST['valor_frete'] ?? 0);
$diaria = brToFloat($_POST['diaria'] ?? 0);
$valor_pedagio = brToFloat($_POST['valor_pedagio'] ?? 0);
$adiantamento = round($valor_frete * 0.7,2);
$frete_final = round($valor_frete * 0.3,2);
$observacoes = $_POST['observacoes'] ?? '';

try {
    $sql = "UPDATE cadastros SET
        id_contrato=:id_contrato, data_coleta=:data_coleta, operacao=:operacao,
        contratante=:contratante, contato=:contato, tem_motorista=:tem_motorista,
        motorista_nome=:motorista_nome, motorista_cpf=:motorista_cpf,
        tipo_documento=:tipo_documento, documento=:documento, pis=:pis,
        data_nascimento=:data_nascimento, tipo_veiculo=:tipo_veiculo, placa_veiculo=:placa_veiculo,
        eixos=:eixos, fornecedor=:fornecedor, origem=:origem, destino=:destino,
        valor_frete=:valor_frete, diaria=:diaria, valor_pedagio=:valor_pedagio,
        adiantamento=:adiantamento, frete_final=:frete_final, observacoes=:observacoes
        WHERE id=:id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_contrato'=>$id_contrato,
        ':data_coleta'=>$data_coleta,
        ':operacao'=>$operacao,
        ':contratante'=>$contratante,
        ':contato'=>$contato,
        ':tem_motorista'=>$tem_motorista,
        ':motorista_nome'=>$motorista_nome,
        ':motorista_cpf'=>$motorista_cpf,
        ':tipo_documento'=>$tipo_documento,
        ':documento'=>$documento,
        ':pis'=>$pis,
        ':data_nascimento'=>$data_nascimento,
        ':tipo_veiculo'=>$tipo_veiculo,
        ':placa_veiculo'=>$placa_veiculo,
        ':eixos'=>$eixos,
        ':fornecedor'=>$fornecedor,
        ':origem'=>$origem,
        ':destino'=>$destino,
        ':valor_frete'=>$valor_frete,
        ':diaria'=>$diaria,
        ':valor_pedagio'=>$valor_pedagio,
        ':adiantamento'=>$adiantamento,
        ':frete_final'=>$frete_final,
        ':observacoes'=>$observacoes,
        ':id'=>$id
    ]);

    $new_data = [
        'id_contrato'=>$id_contrato,
        'data_coleta'=>$data_coleta,
        'operacao'=>$operacao,
        'contratante'=>$contratante,
        'tem_motorista'=>$tem_motorista,
        'motorista_nome'=>$motorista_nome,
        'motorista_cpf'=>$motorista_cpf,
        'contato'=>$contato,
        'tipo_documento'=>$tipo_documento,
        'documento'=>$documento,
        'pis'=>$pis,
        'data_nascimento'=>$data_nascimento,
        'tipo_veiculo'=>$tipo_veiculo,
        'placa_veiculo'=>$placa_veiculo,
        'eixos'=>$eixos,
        'fornecedor'=>$fornecedor,
        'origem'=>$origem,
        'destino'=>$destino,
        'valor_frete'=>$valor_frete,
        'diaria'=>$diaria,
        'valor_pedagio'=>$valor_pedagio,
        'adiantamento'=>$adiantamento,
        'frete_final'=>$frete_final,
        'observacoes'=>$observacoes
    ];

    log_audit($pdo, 'cadastros', $id, 'update', $old_data, $new_data);

    header('Location: ../public/painel.php?msg=sucesso');
    exit;

} catch (PDOException $e) {
    header('Location: ../public/painel.php?msg=erro&detalhe=' . urlencode($e->getMessage()));
    exit;
}
?>
