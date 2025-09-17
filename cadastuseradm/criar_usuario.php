<?php
// criar_usuario.php
session_start();
require_once '../config/database.php';
$pdo = Conexao::conectar();

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = trim($_POST['tipo'] ?? 'usuario'); // padrão: usuario

    if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
        $mensagem = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Email inválido.";
    } else {
        // Gera hash da senha
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo) 
                    VALUES (:nome, :email, :senha, :tipo)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $hash,
                ':tipo' => $tipo
            ]);
            $mensagem = "Usuário criado com sucesso!";
        } catch (Exception $e) {
            $mensagem = "Erro ao criar usuário: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h1>Criar Novo Usuário</h1>
    <?php if($mensagem): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Tipo</label>
            <select name="tipo" class="form-select" required>
                <option value="usuario" selected>Usuário R</option>
                <option value="admin">Administrador</option>
                <option value="moderador">Usuário M</option>
            </select>
        </div>
        <button class="btn btn-primary">Criar Usuário</button>
    </form>
</div>
</body>
</html>
