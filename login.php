<?php
session_start();
include 'confing.inc.php';

// --- ADAPTADOR DE CONEXÃO ---
// Isso garante que funcione tanto se a variável for $conn quanto $conexao
if (isset($conn) && !isset($conexao)) {
    $conexao = $conn;
}

if (!isset($conexao)) {
    die("Erro: Conexão com o banco de dados não encontrada. Verifique o arquivo confing.inc.php");
}
// ----------------------------

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conexao, $_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Preencha e-mail e senha.";
    } else {
        // Busca o usuário
        $sql = "SELECT id, nome, senha FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query($conexao, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $usuario = mysqli_fetch_assoc($resultado);

            // Verifica a senha
            if (password_verify($senha, $usuario['senha'])) {
                
                // Login Sucesso: Salva na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                // --- REDIRECIONAMENTO ---
                // Já coloquei pag.php pois index.php deu erro antes
                header("Location: pag.php"); 
                exit();
                
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "E-mail não encontrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login | Mundo Gamer Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #121212; color: #ffffff; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: Arial, sans-serif; }
        .login-container { background-color: #1a1a1a; padding: 40px; border-radius: 8px; box-shadow: 0 0 15px rgba(46, 204, 113, 0.2); width: 100%; max-width: 400px; }
        .login-container h2 { color: #2ecc71; text-align: center; margin-bottom: 25px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .input-group input { width: 100%; padding: 10px; background-color: #222; border: 1px solid #333; border-radius: 4px; color: #fff; box-sizing: border-box; }
        .btn-login { width: 100%; padding: 12px; background-color: #2ecc71; border: none; border-radius: 4px; color: #1a1a1a; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-login:hover { background-color: #39e588; }
        .message { padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 15px; }
        .error-message { background-color: #e74c3c; color: #fff; }
        .success-message { background-color: #2ecc71; color: #1a1a1a; }
        .register-link { text-align: center; margin-top: 15px; }
        .register-link a { color: #2ecc71; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Acessar Conta</h2>

    <?php 
    if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso') {
        echo '<div class="message success-message">Cadastro realizado! Faça login.</div>';
    }
    
    if ($erro) {
        echo '<div class="message error-message">' . $erro . '</div>';
    } 
    ?>

    <form method="POST" action="login.php">
        <div class="input-group">
            <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
            <input type="email" name="email" required>
        </div>
        <div class="input-group">
            <label for="senha"><i class="fas fa-lock"></i> Senha</label>
            <input type="password" name="senha" required>
        </div>
        <button type="submit" class="btn-login">ENTRAR</button>
    </form>

    <p class="register-link">Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
</div>

</body>
</html>