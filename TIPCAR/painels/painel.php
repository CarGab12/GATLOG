<?php
session_start();
include('../config/conexao.php');
$pdo = Conexao::conectar();

if (!isset($_SESSION['logado']) || !isset($_SESSION['usuario_id'])) {
    header('Location: ../login/login.php');
    exit;
}

// Buscar nome do usuário no banco
$stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$usuario_nome = $usuario['nome'] ?? 'Usuário';

// pega cadastros
$sql = "SELECT * FROM cadastros ORDER BY id DESC";
$stmt = $pdo->query($sql);
$cadastros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel - TIPCAR</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="st.css">
    <style>
      .modal-body .form-label { font-weight:600; }
      .readonly { background:#f7f7f7; }
      .modal-body { max-height: 70vh; overflow-y: auto; }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">TIPCAR</a>
    <div class="d-flex align-items-center">
      <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <?= htmlspecialchars($usuario_nome) ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li><a class="dropdown-item" href="../login/logout.php">Sair</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>



<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0">Painel de Cadastros</h1>
        <a href="../formu/cadastar.php" class="btn btn-primary">Cadastrar Novo</a>
    </div>

    <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Operação</th>
                <th>Contratante</th>
                <th>Origem</th>
                <th>Destino</th>
                <th>Valor do Frete</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cadastros as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['operacao']) ?></td>
                <td><?= htmlspecialchars($c['contratante']) ?></td>
                <td><?= htmlspecialchars($c['origem']) ?></td>
                <td><?= htmlspecialchars($c['destino']) ?></td>
                <td>R$ <?= number_format($c['valor_frete'], 2, ',', '.') ?></td>
                <td>
                    <button class="btn btn-sm btn-info"
                        data-bs-toggle="modal"
                        data-bs-target="#modalCadastro"
                        data-cadastro='<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                        Ver Mais
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalCadastro" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <form action="update.php" method="POST" id="formEditar">
        <div class="modal-header">
          <h5 class="modal-title">Detalhes do Cadastro</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="id">

          <div class="row g-3">
            <!-- ID Contrato -->
            <div class="col-md-4">
              <label class="form-label">ID Contrato</label>
              <input type="text" name="id_contrato" id="id_contrato" class="form-control readonly" readonly>
            </div>

            <!-- Data Coleta -->
            <div class="col-md-4">
              <label class="form-label">Data Coleta</label>
              <input type="text" name="data_coleta" id="data_coleta" class="form-control readonly" placeholder="DD/MM/AAAA" readonly>
            </div>

            <!-- Operação -->
            <div class="col-md-4">
              <label class="form-label">Operação</label>
              <select name="operacao" id="operacao" class="form-control" disabled>
                <option value="">Selecione</option>
                <option value="Eletro">Eletro</option>
                <option value="Indústria">Indústria</option>
                <option value="Móveis">Móveis</option>
                <option value="Atacado">Atacado</option>
              </select>
            </div>

            <!-- Contratante -->
            <div class="col-md-6">
              <label class="form-label">Contratante</label>
              <input type="text" name="contratante" id="contratante" class="form-control readonly" readonly>
            </div>

            <!-- Contato -->
            <div class="col-md-6">
              <label class="form-label">Contato (Telefone)</label>
              <input type="text" name="contato" id="contato" class="form-control readonly" readonly>
            </div>

            <!-- Motorista? -->
            <div class="col-md-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="tem_motorista_modal" name="tem_motorista" value="1" disabled>
                <label class="form-check-label" for="tem_motorista_modal">Motorista?</label>
              </div>
            </div>

            <!-- Motorista campos -->
            <div id="motorista_group" class="row g-3" style="display:none">
              <div class="col-md-6">
                <label class="form-label">Nome do Motorista</label>
                <input type="text" name="motorista_nome" id="motorista_nome" class="form-control readonly" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">CPF do Motorista</label>
                <input type="text" name="motorista_cpf" id="motorista_cpf" class="form-control readonly" readonly>
              </div>
            </div>

            <!-- Tipo documento (select) -->
            <div class="col-md-4">
              <label class="form-label">Tipo Documento</label>
              <select name="tipo_documento" id="tipo_documento_modal" class="form-control" disabled>
                  <option value="">Selecione</option>
                  <option value="CPF">CPF</option>
                  <option value="CNPJ">CNPJ</option>
              </select>
            </div>

            <div class="col-md-8">
              <label class="form-label">Documento (CPF/CNPJ)</label>
              <input type="text" name="documento" id="documento" class="form-control readonly" readonly>
            </div>

            <!-- Outros campos continuam igual -->
            <div class="col-md-4"><label class="form-label">PIS</label><input type="text" name="pis" id="pis" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Data de Nascimento</label><input type="text" name="data_nascimento" id="data_nascimento" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Tipo Veículo</label><input type="text" name="tipo_veiculo" id="tipo_veiculo" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Placa Veículo</label><input type="text" name="placa_veiculo" id="placa_veiculo" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Eixos</label><input type="number" name="eixos" id="eixos" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Fornecedor</label><input type="text" name="fornecedor" id="fornecedor" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Origem</label><input type="text" name="origem" id="origem" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Destino</label><input type="text" name="destino" id="destino" class="form-control readonly" readonly></div>

            <div class="col-md-4"><label class="form-label">Valor do Frete</label><input type="text" name="valor_frete" id="valor_frete" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Diária</label><input type="text" name="diaria" id="diaria" class="form-control readonly" readonly></div>
            <div class="col-md-4"><label class="form-label">Pedágio (R$)</label><input type="text" name="valor_pedagio" id="valor_pedagio" class="form-control readonly" readonly></div>

            <div class="col-md-6"><label class="form-label">Adiantamento (70%)</label><input type="text" name="adiantamento" id="adiantamento" class="form-control readonly" readonly></div>
            <div class="col-md-6"><label class="form-label">Frete Final (30%)</label><input type="text" name="frete_final" id="frete_final" class="form-control readonly" readonly></div>

            <div class="col-12"><label class="form-label">Observações</label><textarea name="observacoes" id="observacoes" class="form-control readonly" rows="3" readonly></textarea></div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="btnEditar">Editar</button>
          <button type="submit" class="btn btn-primary" id="btnSalvar" disabled>Salvar</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function sqlToBR(dateSql){ if(!dateSql) return ''; const p=dateSql.split('-'); return p.length===3?p[2]+'/'+p[1]+'/'+p[0]:dateSql; }
function formatCurrencyBR(num){ if(num==null||num==='') return ''; const n=parseFloat(num); return isNaN(n)?'':n.toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}); }

$('#modalCadastro').on('show.bs.modal', function(event){
    const btn = event.relatedTarget;
    const c = JSON.parse(btn.getAttribute('data-cadastro'));

    $('#id').val(c.id ?? '');
    $('#id_contrato').val(c.id_contrato ?? '');
    $('#data_coleta').val(c.data_coleta?sqlToBR(c.data_coleta):'');
    $('#operacao').val(c.operacao ?? '');
    $('#contratante').val(c.contratante ?? '');
    $('#contato').val(c.contato ?? '');
    $('#tipo_documento_modal').val(c.tipo_documento ?? '');
    $('#documento').val(c.documento ?? '');
    $('#pis').val(c.pis ?? '');
    $('#data_nascimento').val(c.data_nascimento?sqlToBR(c.data_nascimento):'');
    $('#tipo_veiculo').val(c.tipo_veiculo ?? '');
    $('#placa_veiculo').val(c.placa_veiculo ?? '');
    $('#eixos').val(c.eixos ?? '');
    $('#fornecedor').val(c.fornecedor ?? '');
    $('#origem').val(c.origem ?? '');
    $('#destino').val(c.destino ?? '');
    $('#valor_frete').val(formatCurrencyBR(c.valor_frete));
    $('#diaria').val(formatCurrencyBR(c.diaria));
    $('#valor_pedagio').val(formatCurrencyBR(c.valor_pedagio));

    const v=parseFloat(c.valor_frete)||0;
    $('#adiantamento').val(formatCurrencyBR(v*0.7));
    $('#frete_final').val(formatCurrencyBR(v*0.3));

    $('#observacoes').val(c.observacoes ?? '');

    const hasMotorista = (c.tem_motorista==1) || (c.motorista_nome && c.motorista_nome.length>0);
    if(hasMotorista){
        $('#tem_motorista_modal').prop('checked',true);
        $('#motorista_group').show();
        $('#motorista_nome').val(c.motorista_nome ?? '');
        $('#motorista_cpf').val(c.motorista_cpf ?? '');
    } else {
        $('#tem_motorista_modal').prop('checked',false);
        $('#motorista_group').hide();
        $('#motorista_nome').val('');
        $('#motorista_cpf').val('');
    }

    $('#formEditar').find('input,select,textarea').prop('readonly', true).prop('disabled', false);
    $('#btnEditar').prop('disabled', false);
    $('#btnSalvar').prop('disabled', true);

    $('#motorista_cpf').mask('000.000.000-00');
    $('#contato').mask('(00) 00000-0000');
    $('#documento').unmask();
    if(c.tipo_documento==='CPF') $('#documento').mask('000.000.000-00');
    if(c.tipo_documento==='CNPJ') $('#documento').mask('00.000.000/0000-00');
    $('#valor_frete,#diaria,#valor_pedagio').mask('#.##0,00',{reverse:true});
});

$('#btnEditar').on('click', function(){
    $('#formEditar').find('input,select,textarea').prop('readonly',false);
    $('#id').prop('readonly',true);
    $('#btnSalvar').prop('disabled',false);
    $(this).prop('disabled',true);
    $('#tem_motorista_modal').prop('disabled',false).on('change', function(){
        if($(this).is(':checked')){
            $('#motorista_group').show();
            $('#motorista_nome,#motorista_cpf').prop('required',true);
            $('#motorista_cpf').mask('000.000.000-00');
        } else {
            $('#motorista_group').hide();
            $('#motorista_nome,#motorista_cpf').prop('required',false).val('').unmask();
        }
    });
});
</script>
</body>
</html>
