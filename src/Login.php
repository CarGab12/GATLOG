<?php
if (!isset($_SESSION)) session_start();
require_once '../config/database.php';
$pdo = Conexao::conectar();

$erro = '';

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

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['logado'] = true;
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nome'];
            $_SESSION['tipo'] = $usuario['tipo']; 

            header('Location: ../public/painel.php');
            exit;
        } else {
            $erro = "E-mail ou senha incorretos";
        }
    }
}
?>
