<?php
include('../config/conexao.php');

$pdo = Conexao::conectar();
session_start();

$erro = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido";
    } else {
        $sql = 'SELECT * FROM usuarios WHERE email = :email';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);



        $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

       if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['logado'] = true;
            $_SESSION['usuario_id'] = $usuario['id']; // salva o ID do usuário
            // opcional: salvar nome na sessão para acesso rápido
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: ../painels/painel.php');
            exit;


        } else {
            $erro = "E-mail ou senha incorretos";
        }
    }
}

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="st.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

</head>

<body>
    <div class="login-container">

        <img src="../img/TIPCAR.png" alt="Logo">
        
        <h1>Login</h1>
        <hr>
        <br>

        <?php if (!empty($erro)): ?>
            <div class="erro-msg"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>



        <form action="" method="POST">
            <label for="email">E-mail</label>
            <input type="text" placeholder="Digite seu e-mail" name="email" id="email">

            <label for="senha">Senha</label>
            <input type="password" placeholder="Digite sua senha" name="senha" id="senha">

            <button type="submit">Acessar</button>
        </form>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="validarForms.js"></script>

</html>