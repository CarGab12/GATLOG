<?php
include('../config/database.php');
$pdo = Conexao::conectar();
session_start();
$erro = "";
require("../src/Login.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="logincss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

</head>

<body>
    <div class="login-container">
        <img src="../img/gatlogo.png" alt="Logo">
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
<script src="loginjs.js"></script>

</html>