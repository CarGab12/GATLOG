<?php
if (!isset($_SESSION)) session_start();
require_once '../config/database.php';
$pdo = Conexao::conectar();

if (empty($_SESSION['logado']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include('../includes/audit.php');

$erro = "";
$sucesso = "";

function limpa_num($str) { return preg_replace('/\D/', '', (string)$str); }
function parse_currency_br_to_float($valor) {
    if ($valor === null || $valor === '') return 0.0;
    $v = str_replace(['R$', ' '], '', $valor);
    $v = str_replace('.', '', $v);
    $v = str_replace(',', '.', $v);
    return floatval($v);
}
function parse_date_br_to_mysql($data_br) {
    if (empty($data_br)) return null;
    $d = DateTime::createFromFormat('d/m/Y', $data_br);
    return $d ? $d->format('Y-m-d') : null;
}
function format_currency_br($number) { return number_format((float)$number, 2, ',', '.'); }
function valida_cpf($cpf) { }
function valida_cnpj($cnpj) { }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
-
    $operacao = $_POST['operacao'] ?? '';
    $id_contrato = intval($_POST['id_contrato'] ?? 0);
    $data_coleta_br = trim($_POST['data_coleta'] ?? '');
    $contratante = trim($_POST['contratante'] ?? '');
    $tem_motorista = isset($_POST['tem_motorista']) && $_POST['tem_motorista'] === '1';
    $motorista_nome = $tem_motorista ? trim($_POST['motorista_nome'] ?? '') : null;
    $motorista_cpf_raw = $tem_motorista ? trim($_POST['motorista_cpf'] ?? '') : null;
    $contato = trim($_POST['contato'] ?? '');
    $tipo_documento = $_POST['tipo_documento'] ?? '';
    $documento_raw = trim($_POST['documento'] ?? '');
    $pis = trim($_POST['pis'] ?? '');
    $data_nascimento_br = trim($_POST['data_nascimento'] ?? '');
    $tipo_veiculo = trim($_POST['tipo_veiculo'] ?? '');
    $placa_veiculo = trim($_POST['placa_veiculo'] ?? '');
    $eixos = intval($_POST['eixos'] ?? 0);
    $fornecedor = trim($_POST['fornecedor'] ?? '');
    $origem = trim($_POST['origem'] ?? '');
    $destino = trim($_POST['destino'] ?? '');
    $valor_frete_raw = trim($_POST['valor_frete'] ?? '');
    $diaria_raw = trim($_POST['diaria'] ?? '0,00');
    $pedagio_checked = isset($_POST['tem_pedagio']) && ($_POST['tem_pedagio'] === '1' || $_POST['tem_pedagio'] === 'Sim' || $_POST['tem_pedagio'] === 'on');
    $valor_pedagio_raw = trim($_POST['valor_pedagio'] ?? '0,00');
    $observacoes = trim($_POST['observacoes'] ?? '');



    if (empty($erro)) {
        try {
            $data_coleta = parse_date_br_to_mysql($data_coleta_br);
            $data_nascimento = $data_nascimento_br ? parse_date_br_to_mysql($data_nascimento_br) : null;
            $documento_limpo = limpa_num($documento_raw);
            $valor_frete = parse_currency_br_to_float($valor_frete_raw);
            $diaria = parse_currency_br_to_float($diaria_raw);
            $valor_pedagio = $pedagio_checked ? parse_currency_br_to_float($valor_pedagio_raw) : 0.0;

            $adiantamento_calc = round($valor_frete * 0.7, 2);
            $frete_final_calc = round($valor_frete * 0.3, 2);

            $sql = "INSERT INTO cadastros
                (operacao, id_contrato, data_coleta, contratante, motorista_nome, motorista_cpf, contato, tipo_documento, documento, pis, data_nascimento,
                tipo_veiculo, placa_veiculo, eixos, fornecedor, origem, destino, valor_frete, diaria, tem_pedagio, valor_pedagio, observacoes)
                VALUES
                (:operacao, :id_contrato, :data_coleta, :contratante, :motorista_nome, :motorista_cpf, :contato, :tipo_documento, :documento, :pis, :data_nascimento,
                :tipo_veiculo, :placa_veiculo, :eixos, :fornecedor, :origem, :destino, :valor_frete, :diaria, :tem_pedagio, :valor_pedagio, :observacoes)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':operacao'=>$operacao,
                ':id_contrato'=>$id_contrato,
                ':data_coleta'=>$data_coleta,
                ':contratante'=>$contratante,
                ':motorista_nome'=>$tem_motorista?$motorista_nome:null,
                ':motorista_cpf'=>$tem_motorista?limpa_num($motorista_cpf_raw):null,
                ':contato'=>$contato,
                ':tipo_documento'=>$tipo_documento,
                ':documento'=>$documento_limpo,
                ':pis'=>$pis ?: null,
                ':data_nascimento'=>$data_nascimento,
                ':tipo_veiculo'=>$tipo_veiculo,
                ':placa_veiculo'=>strtoupper($placa_veiculo),
                ':eixos'=>$eixos,
                ':fornecedor'=>$fornecedor,
                ':origem'=>$origem,
                ':destino'=>$destino,
                ':valor_frete'=>$valor_frete,
                ':diaria'=>$diaria,
                ':tem_pedagio'=>$pedagio_checked?'Sim':'Não',
                ':valor_pedagio'=>$valor_pedagio,
                ':observacoes'=>$observacoes
            ]);

            $novo_registro = [
                'id_contrato'=>$id_contrato,
                'data_coleta'=>$data_coleta,
                'operacao'=>$operacao,
                'contratante'=>$contratante,
                'tem_motorista'=>$tem_motorista?1:0,
                'motorista_nome'=>$motorista_nome,
                'motorista_cpf'=>$motorista_cpf_raw?limpa_num($motorista_cpf_raw):null,
                'contato'=>$contato,
                'tipo_documento'=>$tipo_documento,
                'documento'=>$documento_limpo,
                'pis'=>$pis,
                'data_nascimento'=>$data_nascimento,
                'tipo_veiculo'=>$tipo_veiculo,
                'placa_veiculo'=>strtoupper($placa_veiculo),
                'eixos'=>$eixos,
                'fornecedor'=>$fornecedor,
                'origem'=>$origem,
                'destino'=>$destino,
                'valor_frete'=>$valor_frete,
                'diaria'=>$diaria,
                'tem_pedagio'=>$pedagio_checked?'Sim':'Não',
                'valor_pedagio'=>$valor_pedagio,
                'observacoes'=>$observacoes
            ];
            log_audit($pdo, 'cadastros', $pdo->lastInsertId(), 'create', null, $novo_registro);

            $sucesso = "Cadastro realizado com sucesso! Adiantamento: R$ ".format_currency_br($adiantamento_calc)." | Frete final: R$ ".format_currency_br($frete_final_calc);
            $_POST = [];

        } catch (Exception $e) {
            $erro = "Erro ao cadastrar: ".$e->getMessage();
        }
    }
}
?>
