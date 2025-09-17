<?php
session_start();
include('../config/database.php');
$pdo = Conexao::conectar();
require('../src/Cadastrar.php');

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Cadastro TIPCAR</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            font-family: Inter, Arial, sans-serif
        }

        body {
            background: linear-gradient(135deg, #0B4677, #1E88E5, #64B5F6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .form-container {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            width: 960px;
            max-width: 100%;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.22);
        }

        .form-header {
            text-align: center;
            margin-bottom: 12px
        }

        .form-header img {
            width: 200px;
            display: block;
            margin: 0 auto 8px
        }

        .form-header h1 {
            margin: 0;
            font-size: 20px;
            color: #333
        }

        .msg {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
            text-align: center;
            font-weight: 600
        }

        .erro {
            background: #ffe5e5;
            color: #d9534f;
            border: 1px solid #f5c2c2
        }

        .sucesso {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            align-items: start
        }

        .full {
            grid-column: 1/-1
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: 600
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            font-size: 14px;
        }

        input[readonly] {
            background: #f7f7f7
        }

        .small {
            max-width: 180px
        }

        .actions {
            margin-top: 16px;
            display: flex;
            gap: 10px
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            color: #fff;
            background: #0B4677
        }

        .btn:hover {
            background: #1E88E5
        }

        .inline {
            display: flex;
            gap: 8px;
            align-items: center
        }

        .hidden {
            display: none
        }

        @media (max-width:900px) {
            .grid {
                grid-template-columns: 1fr
            }

            .small {
                max-width: 100%
            }
        }
    </style>
</head>

<body>

    <div class="form-container animate__animated animate__fadeIn">
        <div class="form-header">
            <img src="../img/gatlogo.png" alt="gatlogo">
            <h1>Cadastro de Operação</h1>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="msg erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if (!empty($sucesso)): ?>
            <div class="msg sucesso"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form id="cadForm" method="POST" novalidate>
            <div class="grid">
                <div>
                    <label>Operação *</label>
                    <select name="operacao" id="operacao" required>
                        <option value="">Selecione</option>
                        <option value="Eletro" <?= (isset($_POST['operacao']) && $_POST['operacao'] === 'Eletro') ? 'selected' : '' ?>>Eletro</option>
                        <option value="Indústria" <?= (isset($_POST['operacao']) && $_POST['operacao'] === 'Indústria') ? 'selected' : '' ?>>Indústria</option>
                        <option value="Móveis" <?= (isset($_POST['operacao']) && $_POST['operacao'] === 'Móveis') ? 'selected' : '' ?>>Móveis</option>
                        <option value="Atacado" <?= (isset($_POST['operacao']) && $_POST['operacao'] === 'Atacado') ? 'selected' : '' ?>>Atacado</option>
                    </select>
                </div>

                <div>
                    <label>ID Contrato</label>
                    <input type="text" name="id_contrato" id="id_contrato"
                        value="<?= htmlspecialchars($_POST['id_contrato'] ?? '') ?>" placeholder="0 (opcional)">
                </div>

                <div>
                    <label>Data Coleta *</label>
                    <input type="text" name="data_coleta" id="data_coleta" placeholder="DD/MM/AAAA"
                        value="<?= htmlspecialchars($_POST['data_coleta'] ?? '') ?>" required>
                </div>

                <div>
                    <label>Nome Contratante *</label>
                    <input type="text" name="contratante" id="contratante"
                        value="<?= htmlspecialchars($_POST['contratante'] ?? '') ?>" required>
                </div>

                <div class="full">
                    <label class="inline"><input type="checkbox" name="tem_motorista" id="tem_motorista" value="1"
                            <?= isset($_POST['tem_motorista']) ? 'checked' : '' ?>> Motorista?</label>
                </div>

                <div id="motorista_fields" class="full hidden" style="display:block;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label>Nome do Motorista</label>
                            <input type="text" name="motorista_nome" id="motorista_nome"
                                value="<?= htmlspecialchars($_POST['motorista_nome'] ?? '') ?>"
                                placeholder="Nome do motorista">
                        </div>
                        <div>
                            <label>CPF do Motorista</label>
                            <input type="text" name="motorista_cpf" id="motorista_cpf"
                                value="<?= htmlspecialchars($_POST['motorista_cpf'] ?? '') ?>"
                                placeholder="000.000.000-00">
                        </div>
                    </div>
                </div>

                <div>
                    <label>Contato (Telefone) *</label>
                    <input type="text" name="contato" id="contato" class="telefone"
                        value="<?= htmlspecialchars($_POST['contato'] ?? '') ?>" placeholder="(00) 90000-0000" required>
                </div>

                <div>
                    <label>Tipo Documento *</label>
                    <select name="tipo_documento" id="tipo_documento" required>
                        <option value="">Selecione</option>
                        <option value="CPF" <?= (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] === 'CPF') ? 'selected' : '' ?>>CPF</option>
                        <option value="CNPJ" <?= (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] === 'CNPJ') ? 'selected' : '' ?>>CNPJ</option>
                    </select>
                </div>

                <div>
                    <label>Número do Documento *</label>
                    <input type="text" name="documento" id="documento"
                        value="<?= htmlspecialchars($_POST['documento'] ?? '') ?>" placeholder="CPF ou CNPJ" required>
                </div>

                <div>
                    <label>PIS</label>
                    <input type="text" name="pis" id="pis" value="<?= htmlspecialchars($_POST['pis'] ?? '') ?>">
                </div>

                <div>
                    <label>Data de Nascimento</label>
                    <input type="text" name="data_nascimento" id="data_nascimento" placeholder="DD/MM/AAAA"
                        value="<?= htmlspecialchars($_POST['data_nascimento'] ?? '') ?>">
                </div>

                <div>
                    <label>Tipo de Veículo *</label>
                    <input type="text" name="tipo_veiculo" id="tipo_veiculo"
                        value="<?= htmlspecialchars($_POST['tipo_veiculo'] ?? '') ?>" required>
                </div>

                <div>
                    <label>Placa Veículo *</label>
                    <input type="text" name="placa_veiculo" id="placa_veiculo"
                        value="<?= htmlspecialchars($_POST['placa_veiculo'] ?? '') ?>" placeholder="AAA0A00" required>
                </div>

                <div>
                    <label>Eixos</label>
                    <input type="number" name="eixos" id="eixos" min="0"
                        value="<?= htmlspecialchars($_POST['eixos'] ?? '0') ?>">
                </div>

                <div>
                    <label>Fornecedor *</label>
                    <input type="text" name="fornecedor" id="fornecedor"
                        value="<?= htmlspecialchars($_POST['fornecedor'] ?? '') ?>" required>
                </div>

                <div>
                    <label>Origem *</label>
                    <input type="text" name="origem" id="origem" value="<?= htmlspecialchars($_POST['origem'] ?? '') ?>"
                        required>
                </div>

                <div>
                    <label>Destino *</label>
                    <input type="text" name="destino" id="destino"
                        value="<?= htmlspecialchars($_POST['destino'] ?? '') ?>" required>
                </div>

                <div>
                    <label>Valor do Frete * (R$)</label>
                    <input type="text" name="valor_frete" id="valor_frete"
                        value="<?= htmlspecialchars($_POST['valor_frete'] ?? '') ?>" placeholder="0,00" required>
                </div>

                <div>
                    <label>Adiantamento (70%)</label>
                    <input type="text" id="adiantamento" readonly
                        value="<?= isset($adiantamento_calc) ? 'R$ ' . format_currency_br($adiantamento_calc) : '' ?>">
                </div>

                <div>
                    <label>Frete Final (30%)</label>
                    <input type="text" id="frete_final" readonly
                        value="<?= isset($frete_final_calc) ? 'R$ ' . format_currency_br($frete_final_calc) : '' ?>">
                </div>

                <div>
                    <label>Diária (R$)</label>
                    <input type="text" name="diaria" id="diaria"
                        value="<?= htmlspecialchars($_POST['diaria'] ?? '0,00') ?>">
                </div>

                <div class="full">
                    <label class="inline"><input type="checkbox" name="pedagio" id="pedagio" value="1"
                            <?= (isset($_POST['tem_pedagio']) && ($_POST['tem_pedagio'] == '1' || $_POST['tem_pedagio'] == 'Sim')) ? 'checked' : '' ?>> Pedágio?</label>
                </div>

                <div id="pedagio_field"
                    class="<?= (isset($_POST['tem_pedagio']) && ($_POST['tem_pedagio'] == '1' || $_POST['tem_pedagio'] == 'Sim')) ? '' : 'hidden' ?>">
                    <label>Valor do Pedágio (R$)</label>
                    <input type="text" name="valor_pedagio" id="valor_pedagio"
                        value="<?= htmlspecialchars($_POST['valor_pedagio'] ?? '0,00') ?>">
                </div>

                <div>
                    <label>OBS (CARGA)</label>
                    <input type="text" name="observacoes" id="observacoes"
                        value="<?= htmlspecialchars($_POST['observacoes'] ?? '') ?>">
                </div>

            </div>

            <div class="actions full" style="margin-top:12px">
                <button type="submit" class="btn">Cadastrar</button>
            </div>
            <div class="actions full" style="margin-top:12px; ">
                <a href="painel.php" class="btn-center">voltar</a>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {

            function toggleMotoristaFields() {
                if ($('#tem_motorista').is(':checked')) {
                    $('#motorista_fields').removeClass('hidden').show();
                    $('#motorista_nome').prop('required', true);
                    $('#motorista_cpf').prop('required', true);
                    $('#motorista_cpf').mask('000.000.000-00');
                } else {
                    $('#motorista_fields').addClass('hidden').hide();
                    $('#motorista_nome').prop('required', false);
                    $('#motorista_cpf').prop('required', false).val('').unmask();
                }
            }
            toggleMotoristaFields();
            $('#tem_motorista').on('change', toggleMotoristaFields);

            function togglePedagio() {
                if ($('#pedagio').is(':checked')) {
                    $('#pedagio_field').removeClass('hidden').show();
                    $('#valor_pedagio').prop('required', true);
                } else {
                    $('#pedagio_field').addClass('hidden').hide();
                    $('#valor_pedagio').prop('required', false).val('0,00');
                }
            }
            togglePedagio();
            $('#pedagio').on('change', togglePedagio);

            $('#data_coleta, #data_nascimento').mask('00/00/0000');
            $('.telefone').mask('(00) 00000-0000');
            $('#diaria, #valor_frete, #valor_pedagio').mask('#.##0,00', { reverse: true });

            function setupDocumentoMask() {
                var tipo = $('#tipo_documento').val();
                $('#documento').val('');
                if (tipo === 'CPF') {
                    $('#documento').mask('000.000.000-00');
                } else if (tipo === 'CNPJ') {
                    $('#documento').mask('00.000.000/0000-00');
                } else {
                    try { $('#documento').unmask(); } catch (e) { }
                }
            }
            setupDocumentoMask();
            $('#tipo_documento').on('change', setupDocumentoMask);

            function calcularAdiantamento() {
                var raw = ($('#valor_frete').cleanVal && $('#valor_frete').cleanVal()) ? $('#valor_frete').cleanVal() : '';
                var valor = 0;
                if (raw !== '') valor = parseFloat(raw) / 100.0;
                var adiantamento = (valor * 0.7);
                var freteFinal = (valor * 0.3);

                if (!isNaN(adiantamento)) {
                    $('#adiantamento').val(adiantamento.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                } else $('#adiantamento').val('');
                if (!isNaN(freteFinal)) {
                    $('#frete_final').val(freteFinal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                } else $('#frete_final').val('');
            }

            $('#valor_frete').on('keyup change', calcularAdiantamento);
            calcularAdiantamento();

            $('#cadForm').on('submit', function (e) {

                var tipoDoc = $('#tipo_documento').val();
                var doc = $('#documento').val();

                if (!tipoDoc) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Escolha o tipo de documento', text: 'Selecione CPF ou CNPJ.' });
                    return;
                }

                function onlyDigits(s) { return (s || '').replace(/\D/g, ''); }

                function validaCPFJS(cpf) {
                    cpf = onlyDigits(cpf);
                    if (cpf.length !== 11) return false;
                    if (/^(\d)\1+$/.test(cpf)) return false;
                    let sum = 0;
                    for (let i = 0; i < 9; i++) sum += parseInt(cpf.charAt(i)) * (10 - i);
                    let rev = 11 - (sum % 11);
                    if (rev === 10 || rev === 11) rev = 0;
                    if (rev !== parseInt(cpf.charAt(9))) return false;
                    sum = 0;
                    for (let i = 0; i < 10; i++) sum += parseInt(cpf.charAt(i)) * (11 - i);
                    rev = 11 - (sum % 11);
                    if (rev === 10 || rev === 11) rev = 0;
                    if (rev !== parseInt(cpf.charAt(10))) return false;
                    return true;
                }

                function validaCNPJJS(cnpj) {
                    cnpj = onlyDigits(cnpj);
                    if (cnpj.length !== 14) return false;
                    if (/^(\d)\1+$/.test(cnpj)) return false;
                    let tamanho = cnpj.length - 2;
                    let numeros = cnpj.substring(0, tamanho);
                    let digitos = cnpj.substring(tamanho);
                    let soma = 0;
                    let pos = tamanho - 7;
                    for (let i = tamanho; i >= 1; i--) {
                        soma += numeros[tamanho - i] * pos--;
                        if (pos < 2) pos = 9;
                    }
                    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                    if (resultado != digitos.charAt(0)) return false;
                    tamanho = tamanho + 1;
                    numeros = cnpj.substring(0, tamanho);
                    soma = 0;
                    pos = tamanho - 7;
                    for (let i = tamanho; i >= 1; i--) {
                        soma += numeros[tamanho - i] * pos--;
                        if (pos < 2) pos = 9;
                    }
                    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                    if (resultado != digitos.charAt(1)) return false;
                    return true;
                }

                if (tipoDoc === 'CPF' && !validaCPFJS(doc)) {
                    e.preventDefault();
                    Swal.fire({ icon: 'error', title: 'CPF inválido', text: 'Verifique o CPF informado.' });
                    return;
                }
                if (tipoDoc === 'CNPJ' && !validaCNPJJS(doc)) {
                    e.preventDefault();
                    Swal.fire({ icon: 'error', title: 'CNPJ inválido', text: 'Verifique o CNPJ informado.' });
                    return;
                }

                if ($('#tem_motorista').is(':checked')) {
                    if (!$('#motorista_nome').val().trim()) {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Nome do motorista', text: 'Informe o nome do motorista.' });
                        return;
                    }
                    if (!validaCPFJS($('#motorista_cpf').val())) {
                        e.preventDefault();
                        Swal.fire({ icon: 'error', title: 'CPF do motorista inválido', text: 'Verifique o CPF do motorista.' });
                        return;
                    }
                }

                if (!/^\d{2}\/\d{2}\/\d{4}$/.test($('#data_coleta').val())) {
                    e.preventDefault();
                    Swal.fire({ icon: 'error', title: 'Data inválida', text: 'Use formato DD/MM/AAAA para Data de Coleta.' });
                    return;
                }

                var raw = ($('#valor_frete').cleanVal && $('#valor_frete').cleanVal()) ? $('#valor_frete').cleanVal() : '';
                var valor = 0;
                if (raw !== '') valor = parseFloat(raw) / 100.0;
                if (valor <= 0) {
                    e.preventDefault();
                    Swal.fire({ icon: 'error', title: 'Valor do frete inválido', text: 'Informe um valor de frete maior que zero.' });
                    return;
                }

            });

        });
    </script>

</body>

</html>