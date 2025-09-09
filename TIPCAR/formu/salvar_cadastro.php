<?php
include('../config/conexao.php');

$pdo = Conexao::conectar();
session_start();

// Função para converter data BR para formato SQL
function dataParaSQL($data) {
    if (empty($data)) return null;
    $partes = explode('/', $data);
    if (count($partes) == 3) {
        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    }
    return null;
}

// Função para converter moeda BRL (1.234,56) para decimal (1234.56)
function moedaParaSQL($valor) {
    if (empty($valor)) return 0;
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return (float)$valor;
}

// Função simples de validação CPF
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11) return false;
    return true; // pode expandir com validação oficial
}

// Função simples de validação CNPJ
function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) != 14) return false;
    return true; // pode expandir com validação oficial
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operacao       = $_POST['operacao'];
    $id_contrato    = $_POST['id_contrato'] ?? 0;
    $data_coleta    = dataParaSQL($_POST['data_coleta']);
    $contratante    = trim($_POST['contratante']);
    $tem_motorista  = isset($_POST['tem_motorista']) ? 1 : 0;
    $motorista_nome = $tem_motorista ? trim($_POST['motorista_nome']) : null;
    $motorista_cpf  = $tem_motorista ? trim($_POST['motorista_cpf']) : null;
    $contato        = trim($_POST['contato']);
    $tipo_documento = $_POST['tipo_documento'];
    $documento      = trim($_POST['documento']);
    $pis            = trim($_POST['pis']);
    $data_nascimento= dataParaSQL($_POST['data_nascimento']);
    $tipo_veiculo   = trim($_POST['tipo_veiculo']);
    $placa_veiculo  = trim($_POST['placa_veiculo']);
    $eixos          = (int)$_POST['eixos'];
    $fornecedor     = trim($_POST['fornecedor']);
    $origem         = trim($_POST['origem']);
    $destino        = trim($_POST['destino']);
    $valor_frete    = moedaParaSQL($_POST['valor_frete']);
    $diaria         = moedaParaSQL($_POST['diaria']);
    $observacoes    = trim($_POST['observacoes']);
    $tem_pedagio    = isset($_POST['tem_pedagio']) ? 1 : 0;
    $valor_pedagio  = $tem_pedagio ? moedaParaSQL($_POST['valor_pedagio']) : 0;


    // Calculando adiantamento e frete final
    $adiantamento = $valor_frete * 0.7;
    $frete_final  = $valor_frete * 0.3;

    // Validação de documento
    if ($tipo_documento === 'cpf' && !validarCPF($documento)) {
        die("CPF inválido.");
    }
    if ($tipo_documento === 'cnpj' && !validarCNPJ($documento)) {
        die("CNPJ inválido.");
    }

    try {
        $sql = "INSERT INTO cadastros 
            (operacao, id_contrato, data_coleta, contratante, tem_motorista, motorista_nome, motorista_cpf, 
            contato, tipo_documento, documento, pis, data_nascimento, tipo_veiculo, placa_veiculo, eixos, fornecedor, 
            origem, destino, valor_frete, diaria, observacoes, tem_pedagio, valor_pedagio, adiantamento, frete_final)
            VALUES 
            (:operacao, :id_contrato, :data_coleta, :contratante, :tem_motorista, :motorista_nome, :motorista_cpf, 
            :contato, :tipo_documento, :documento, :pis, :data_nascimento, :tipo_veiculo, :placa_veiculo, :eixos, :fornecedor, 
            :origem, :destino, :valor_frete, :diaria, :observacoes, :tem_pedagio, :valor_pedagio, :adiantamento, :frete_final)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':operacao'       => $operacao,
            ':id_contrato'    => $id_contrato,
            ':data_coleta'    => $data_coleta,
            ':contratante'    => $contratante,
            ':tem_motorista'  => $tem_motorista,
            ':motorista_nome' => $motorista_nome,
            ':motorista_cpf'  => $motorista_cpf,
            ':contato'        => $contato,
            ':tipo_documento' => $tipo_documento,
            ':documento'      => $documento,
            ':pis'            => $pis,
            ':data_nascimento'=> $data_nascimento,
            ':tipo_veiculo'   => $tipo_veiculo,
            ':placa_veiculo'  => $placa_veiculo,
            ':eixos'          => $eixos,
            ':fornecedor'     => $fornecedor,
            ':origem'         => $origem,
            ':destino'        => $destino,
            ':valor_frete'    => $valor_frete,
            ':diaria'         => $diaria,
            ':tem_pedagio'    => $tem_pedagio,
            ':valor_pedagio'  => $valor_pedagio,
            ':adiantamento'   => $adiantamento,
            ':frete_final'    => $frete_final,
            ':observacoes', $observacoes,
        ]);

        echo "Cadastro realizado com sucesso!";
        header("Location: ../painels/painel.php");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>
