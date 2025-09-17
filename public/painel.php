<?php
if (!isset($_SESSION))
  session_start();
require_once '../config/database.php';
$pdo = Conexao::conectar();


if (empty($_SESSION['logado']) || empty($_SESSION['user_id'])) {
  header('Location: ../public/login.php');
  exit;
}



$usuario_nome = $_SESSION['user_name'] ?? 'Usuário';


$sql = "SELECT * FROM cadastros ORDER BY id DESC";
$stmt = $pdo->query($sql);
$cadastros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Painel - GATLOG</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="painelcss.css">
  <style>

  </style>
</head>

<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
      <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral">
        <i class="bi bi-list"></i>
      </button>
      <a class="navbar-brand" href="#">GATLOG</a>
      <div class="d-flex align-items-center ms-auto">
        <div class="dropdown">
          <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <?= htmlspecialchars($usuario_nome) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../src/logout.php">Sair</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <div class="offcanvas offcanvas-start" id="menuLateral">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">GATLOG</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <hr>
    <div class="offcanvas-body">
      <ul class="list-group">
        <li class="list-group-item">
          <a href="painel.php" class="text-decoration-none"><i class="bi bi-check-circle me-2"></i> Fretes Autonomos</a>
        </li>
      </ul>
    </div>
  </div>

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="m-0">Painel de Cadastros</h1>
      <div>
        <a href="cadastrar.php" class="btn btn-primary">Cadastrar Novo</a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRelatorio">Gerar Relatório</button>
      </div>
    </div>
    <div class="container mb-4">
      <div class="row g-3 align-items-end">

        <div class="col-md-2">
          <label for="filtroContrato" class="form-label">Contrato</label>
          <input type="text" id="filtroContrato" class="form-control" placeholder="Filtrar por Contrato">
        </div>

        <div class="col-md-2">
          <label for="filtroCarga" class="form-label">Carga</label>
          <input type="text" id="filtroCarga" class="form-control" placeholder="Filtrar por Carga">
        </div>

        <div class="col-md-2">
          <label for="filtroOperacao" class="form-label">Operação</label>
          <input type="text" id="filtroOperacao" class="form-control" placeholder="Filtrar por Operação">
        </div>

        <div class="col-md-2">
          <label for="filtroContratante" class="form-label">Contratante</label>
          <input type="text" id="filtroContratante" class="form-control" placeholder="Filtrar por Contratante">
        </div>

        <div class="col-md-2">
          <label for="filtroDestino" class="form-label">Destino</label>
          <input type="text" id="filtroDestino" class="form-control" placeholder="Filtrar por Destino">
        </div>

        <div class="col-md-1">
          <label for="filtroDataInicio" class="form-label">Início</label>
          <input type="date" id="filtroDataInicio" class="form-control">
        </div>

        <div class="col-md-1">
          <label for="filtroDataFim" class="form-label">Fim</label>
          <input type="date" id="filtroDataFim" class="form-control">
        </div>

      </div>

    </div>

    <table class="table table-striped table-bordered">
      <thead class="table-primary">
        <tr>
          <th>Contrato</th>
          <th>Operação</th>
          <th>Contratante</th>
          <th>Destino</th>
          <th>Valor do Frete</th>
          <th>Data de Criação</th>
          <th>Carga</th>
          <th>Ações</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cadastros as $c): ?>
          <tr>
            <td><?= $c['id_contrato'] ?></td>
            <td><?= htmlspecialchars($c['operacao']) ?></td>
            <td><?= htmlspecialchars($c['contratante']) ?></td>
            <td><?= htmlspecialchars($c['destino']) ?></td>
            <td>R$ <?= number_format($c['valor_frete'], 2, ',', '.') ?></td>
            <td><?= date('d/m/Y', strtotime($c['criado_em'])) ?></td>
            <td><?= htmlspecialchars($c['observacoes']) ?></td>
            <td>
              <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalCadastro"
                data-cadastro='<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                Ver Mais
              </button>
            </td>
            <td>
              <?php if ($_SESSION['tipo'] == 1): ?>
                <button
                  class="status-btn <?= ($c['status'] === 'pendente') ? 'status-pendente' : (($c['status'] === 'parcial') ? 'status-parcial' : 'status-pago') ?>"
                  data-id="<?= $c['id'] ?>" data-status="<?= $c['status'] ?>">
                  <?= ($c['status'] === 'pendente') ? 'Pendente' : (($c['status'] === 'parcial') ? 'Pago 70%' : 'Pago 100%') ?>
                </button>
              <?php else: ?>
                <span
                  class="status-label <?= ($c['status'] === 'pendente') ? 'status-pendente' : (($c['status'] === 'parcial') ? 'status-parcial' : 'status-pago') ?>">
                  <?= ($c['status'] === 'pendente') ? 'Pendente' : (($c['status'] === 'parcial') ? 'Pago 70%' : 'Pago 100%') ?>
                </span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="modal fade" id="modalRelatorio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formRelatorio" action="../src/relatorio.php" method="POST" target="_blank">
          <div class="modal-header">
            <h5 class="modal-title">Selecionar Período</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="data_inicio" class="form-label">Data Início</label>
              <input type="date" name="data_inicio" id="data_inicio" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="data_fim" class="form-label">Data Fim</label>
              <input type="date" name="data_fim" id="data_fim" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalHistorico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Histórico do Cadastro</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <table class="table table-striped table-bordered">
            <thead class="table-primary">
              <tr>
                <th>Ação</th>
                <th>Usuário</th>
                <th>Data / Hora</th>
                <th>Detalhes</th>
              </tr>
            </thead>
            <tbody id="tbodyHistoricoModal">
              <tr>
                <td colspan="4" class="text-center">Carregando histórico...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modalCadastro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <form action="../src/update.php" method="POST" id="formEditar">
          <div class="modal-header">
            <h5 class="modal-title">Detalhes do Cadastro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <input type="hidden" name="id" id="id">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">ID Contrato</label>
                <input type="text" name="id_contrato" id="id_contrato" class="form-control readonly" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label">Data Coleta</label>
                <input type="text" name="data_coleta" id="data_coleta" class="form-control readonly"
                  placeholder="DD/MM/AAAA" readonly>
              </div>
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
              <div class="col-md-6">
                <label class="form-label">Contratante</label>
                <input type="text" name="contratante" id="contratante" class="form-control readonly" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">Contato (Telefone)</label>
                <input type="text" name="contato" id="contato" class="form-control readonly" readonly>
              </div>
              <div class="col-md-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="tem_motorista_modal" name="tem_motorista"
                    value="1" disabled>
                  <label class="form-check-label" for="tem_motorista_modal">Motorista?</label>
                </div>
              </div>
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
              <div class="col-md-4"><label class="form-label">PIS</label><input type="text" name="pis" id="pis"
                  class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Data Nascimento</label><input type="text"
                  name="data_nascimento" id="data_nascimento" class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Tipo Veículo</label><input type="text" name="tipo_veiculo"
                  id="tipo_veiculo" class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Placa Veículo</label><input type="text"
                  name="placa_veiculo" id="placa_veiculo" class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Eixos</label><input type="number" name="eixos" id="eixos"
                  class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Fornecedor</label><input type="text" name="fornecedor"
                  id="fornecedor" class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Origem</label><input type="text" name="origem" id="origem"
                  class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Destino</label><input type="text" name="destino"
                  id="destino" class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Valor do Frete</label><input type="text"
                  name="valor_frete" id="valor_frete" class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Diária</label><input type="text" name="diaria" id="diaria"
                  class="form-control readonly" readonly></div>
              <div class="col-md-4"><label class="form-label">Pedágio (R$)</label><input type="text"
                  name="valor_pedagio" id="valor_pedagio" class="form-control readonly" readonly></div>
              <div class="col-md-6"><label class="form-label">Adiantamento (70%)</label><input type="text"
                  name="adiantamento" id="adiantamento" class="form-control readonly" readonly></div>
              <div class="col-md-6"><label class="form-label">Frete Final (30%)</label><input type="text"
                  name="frete_final" id="frete_final" class="form-control readonly" readonly></div>
              <div class="col-12"><label class="form-label">Observações</label><textarea name="observacoes"
                  id="observacoes" class="form-control readonly" rows="3" readonly></textarea></div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="btnEditar">Editar</button>
            <button type="submit" class="btn btn-primary" id="btnSalvar" disabled>Salvar</button>
            <button type="button" class="btn btn-danger" id="btnExcluir">Excluir</button>
            <button type="button" class="btn btn-sm btn-secondary"
              onclick="abrirModalHistorico($('#id').val(), 'cadastros')">Histórico</button>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>


        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>

    document.addEventListener("DOMContentLoaded", function () {
      function updateButtonUI(btn, status) {
        btn.dataset.status = status;
        btn.classList.remove('status-pendente', 'status-parcial', 'status-pago');

        if (status === 'pendente') {
          btn.classList.add('status-pendente');
          btn.textContent = 'Pendente';
          btn.style.color = '#fff';
        } else if (status === 'parcial') {
          btn.classList.add('status-parcial');
          btn.textContent = 'Pago 70%';
          btn.style.color = '#000';
        } else if (status === 'pago') {
          btn.classList.add('status-pago');
          btn.textContent = 'Pago 100%';
          btn.style.color = '#fff';
        }
      }

      document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
          const id = this.dataset.id;
          if (!id) {
            alert('ID não encontrado no botão (data-id).');
            return;
          }

          const oldStatus = this.dataset.status || (
            this.classList.contains('status-pendente') ? 'pendente' :
              this.classList.contains('status-parcial') ? 'parcial' : 'pago'
          );

          let newStatus;
          if (oldStatus === 'pendente') newStatus = 'parcial';
          else if (oldStatus === 'parcial') newStatus = 'pago';
          else newStatus = 'pendente';

          updateButtonUI(this, newStatus);

          try {
            const resp = await fetch('update_status.php', { 
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
              body: 'id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(newStatus)
            });

            const json = await resp.json();

            if (!json || json.success !== true) {
              updateButtonUI(this, oldStatus);
              console.error('Resposta do servidor:', json);
              alert('Erro ao salvar status: ' + (json.message || 'erro desconhecido'));
            } else {
            }
          } catch (err) {
            updateButtonUI(this, oldStatus);
            console.error('Erro fetch:', err);
            alert('Erro de rede ao salvar status: ' + err.message);
          }
        });
      });
    });

    function abrirModalHistorico(id, tabela) {
      const modalEl = document.getElementById('modalHistorico');
      const modal = new bootstrap.Modal(modalEl);

      modalEl.style.zIndex = 1200;

      modalEl.addEventListener('shown.bs.modal', () => {
        const backdrop = document.querySelector('.modal-backdrop:last-child');
        if (backdrop) {
          backdrop.style.zIndex = 1190;
        }
      });

      modal.show();

      fetch(`get_historico.php?id=${id}&tabela=${tabela}`)
        .then(resp => resp.json())
        .then(data => {
          const tbody = document.getElementById('tbodyHistoricoModal');
          if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">Nenhum histórico encontrado</td></tr>';
            return;
          }

          let html = '';
          data.forEach(row => {
            let detalhes = '';
            if (row.alteracoes && typeof row.alteracoes === 'object' && Object.keys(row.alteracoes).length > 0) {
              detalhes += '<ul>';
              for (const campo in row.alteracoes) {
                detalhes += `<li><b>${campo}:</b> de <span style="color:red">${row.alteracoes[campo].de ?? '-'}</span> → <span style="color:green">${row.alteracoes[campo].para ?? '-'}</span></li>`;
              }
              detalhes += '</ul>';
            } else {
              detalhes = '<i>Sem alterações de dados</i>';
            }

            html += `<tr>
                    <td>${row.action}</td>
                    <td>${row.usuario ?? '-'}</td>
                    <td>${row.data_hora}</td>
                    <td>${detalhes}</td>
                </tr>`;
          });

          tbody.innerHTML = html;
        })
        .catch(err => {
          console.error('Erro ao carregar histórico:', err);
          document.getElementById('tbodyHistoricoModal').innerHTML = '<tr><td colspan="4">Erro ao carregar histórico</td></tr>';
        });
    }

    function aplicarFiltros() {
      const contrato = $('#filtroContrato').val().toLowerCase();
      const carga = $('#filtroCarga').val().toLowerCase();
      const operacao = $('#filtroOperacao').val().toLowerCase();
      const contratante = $('#filtroContratante').val().toLowerCase();
      const destino = $('#filtroDestino').val().toLowerCase();
      const dataInicio = $('#filtroDataInicio').val();
      const dataFim = $('#filtroDataFim').val();

      $('table tbody tr').each(function () {
        let mostrar = true;

        const colContrato = $(this).find('td').eq(0).text().toLowerCase();
        const colOperacao = $(this).find('td').eq(1).text().toLowerCase();
        const colContratante = $(this).find('td').eq(2).text().toLowerCase();
        const colDestino = $(this).find('td').eq(3).text().toLowerCase();
        const colCarga = $(this).find('td').eq(6).text().toLowerCase();
        const colData = $(this).find('td').eq(5).text().trim();
        if (contrato && !colContrato.includes(contrato)) mostrar = false;
        if (operacao && !colOperacao.includes(operacao)) mostrar = false;
        if (contratante && !colContratante.includes(contratante)) mostrar = false;
        if (destino && !colDestino.includes(destino)) mostrar = false;
        if (carga && !colCarga.includes(carga)) mostrar = false;



        if (colData) {
          const dataRow = colData.split('/').reverse().join('-');
          if (dataInicio && dataRow < dataInicio) mostrar = false;
          if (dataFim && dataRow > dataFim) mostrar = false;
        }

        $(this).toggle(mostrar);
      });
    }

    $('#filtroContrato, #filtroCarga, #filtroOperacao, #filtroContratante, #filtroDestino, #filtroDataInicio, #filtroDataFim')
      .on('input change', aplicarFiltros);

    $(document).ready(function () {
      let hoje = new Date();
      let fim = hoje.toISOString().split('T')[0];
      let inicio = new Date();
      inicio.setDate(hoje.getDate() - 30);
      let inicioStr = inicio.toISOString().split('T')[0];

      $('#filtroDataInicio').val(inicioStr);
      $('#filtroDataFim').val(fim);

      aplicarFiltros();
    });


    function sqlToBR(dateSql) {
      if (!dateSql) return '';
      const p = dateSql.split('-');
      return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : dateSql;
    }
    function formatCurrencyBR(num) {
      if (num == null || num === '') return '';
      const n = parseFloat(num);
      return isNaN(n) ? '' : n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    $('#modalCadastro').on('show.bs.modal', function (event) {
      const btn = event.relatedTarget;
      const c = JSON.parse(btn.getAttribute('data-cadastro'));

      $('#id').val(c.id ?? '');
      $('#id_contrato').val(c.id_contrato ?? '');
      $('#data_coleta').val(c.data_coleta ? sqlToBR(c.data_coleta) : '');
      $('#operacao').val(c.operacao ?? '');
      $('#contratante').val(c.contratante ?? '');
      $('#contato').val(c.contato ?? '');
      $('#tipo_documento_modal').val(c.tipo_documento ?? '');
      $('#documento').val(c.documento ?? '');
      $('#pis').val(c.pis ?? '');
      $('#data_nascimento').val(c.data_nascimento ? sqlToBR(c.data_nascimento) : '');
      $('#tipo_veiculo').val(c.tipo_veiculo ?? '');
      $('#placa_veiculo').val(c.placa_veiculo ?? '');
      $('#eixos').val(c.eixos ?? '');
      $('#fornecedor').val(c.fornecedor ?? '');
      $('#origem').val(c.origem ?? '');
      $('#destino').val(c.destino ?? '');
      $('#valor_frete').val(formatCurrencyBR(c.valor_frete));
      $('#diaria').val(formatCurrencyBR(c.diaria));
      $('#valor_pedagio').val(formatCurrencyBR(c.valor_pedagio));

      const v = parseFloat(c.valor_frete) || 0;
      $('#adiantamento').val(formatCurrencyBR(v * 0.7));
      $('#frete_final').val(formatCurrencyBR(v * 0.3));
      $('#observacoes').val(c.observacoes ?? '');

      const hasMotorista = (c.tem_motorista == 1) || (c.motorista_nome && c.motorista_nome.length > 0);
      $('#tem_motorista_modal').prop('checked', hasMotorista);
      $('#motorista_group').toggle(hasMotorista);
      $('#motorista_nome').val(c.motorista_nome ?? '');
      $('#motorista_cpf').val(c.motorista_cpf ?? '');

      $('#formEditar').find('input,select,textarea').prop('readonly', true).prop('disabled', false);
      $('#btnEditar').prop('disabled', false);
      $('#btnSalvar').prop('disabled', true);

      $('#motorista_cpf').mask('000.000.000-00');
      $('#contato').mask('(00) 00000-0000');
      $('#documento').unmask();
      if (c.tipo_documento === 'CPF') $('#documento').mask('000.000.000-00');
      if (c.tipo_documento === 'CNPJ') $('#documento').mask('00.000.000/0000-00');
      $('#valor_frete,#diaria,#valor_pedagio').mask('#.##0,00', { reverse: true });
    });

    $('#btnEditar').on('click', function () {
      $('#formEditar').find('input,select,textarea').prop('readonly', false);
      $('#id').prop('readonly', true);
      $('#btnSalvar').prop('disabled', false);
      $(this).prop('disabled', true);
    });

    $('#tem_motorista_modal').on('change', function () {
      if ($(this).is(':checked')) {
        $('#motorista_group').show();
        $('#motorista_nome,#motorista_cpf').prop('required', true);
        $('#motorista_cpf').mask('000.000.000-00');
      } else {
        $('#motorista_group').hide();
        $('#motorista_nome,#motorista_cpf').prop('required', false).val('').unmask();
      }
    });

    $('#btnExcluir').on('click', function () {
      if (!confirm('Tem certeza que deseja excluir este cadastro?')) return;
      const id = $('#id').val();
      $.post('../src/delete.php', { id: id }, function (resp) {
        if (resp.success) {
          alert('Cadastro excluído com sucesso!');
          location.reload();
        } else {
          alert('Erro ao excluir: ' + (resp.msg || ''));
        }
      }, 'json');
    });

  </script>
</body>

</html>