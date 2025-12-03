<?php
include 'confing.inc.php'; 


if (isset($conn) && !isset($conexao)) {
    $conexao = $conn;
}


if (!isset($conexao) || !$conexao) {
    die("ERRO FATAL: Não foi possível conectar ao banco de dados. Verifique se o arquivo 'confing.inc.php' está criando a variável '$conn' ou '$conexao'.");
}

$nome = '';
$email = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

   
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $erro = "Todos os campos são obrigatórios.";
    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres.";
    } else {
        
        
        $email_seguro = mysqli_real_escape_string($conexao, $email);
        $sql_check = "SELECT email FROM usuarios WHERE email = '$email_seguro'";
        $result_check = mysqli_query($conexao, $sql_check); 

        if (mysqli_num_rows($result_check) > 0) {
            $erro = "Este e-mail já está cadastrado.";
        } else {
            
            $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);
            
            $sql_insert = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conexao, $sql_insert);
            mysqli_stmt_bind_param($stmt, "sss", $nome, $email, $senha_hashed);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php?cadastro=sucesso");
                exit();
            } else {
                $erro = "Erro ao cadastrar: " . mysqli_error($conexao);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro | Mundo Gamer Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #121212; color: #ffffff; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: sans-serif; }
        .register-container { background-color: #1a1a1a; padding: 40px; border-radius: 8px; box-shadow: 0 0 15px rgba(46, 204, 113, 0.2); width: 100%; max-width: 400px; }
        .register-container h2 { color: #2ecc71; text-align: center; margin-bottom: 25px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .input-group input { width: 100%; padding: 10px; background-color: #222; border: 1px solid #333; border-radius: 4px; color: #fff; box-sizing: border-box; }
        .btn-register { width: 100%; padding: 12px; background-color: #2ecc71; border: none; border-radius: 4px; color: #1a1a1a; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-register:hover { background-color: #39e588; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .error-message { background-color: #e74c3c; color: #fff; }
        .login-link { text-align: center; margin-top: 15px; }
        .login-link a { color: #2ecc71; text-decoration: none; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Crie sua Conta</h2>

    <?php if ($erro): ?>
        <p class="message error-message"><?php echo $erro; ?></p>
    <?php endif; ?>
    
    <form method="POST" action="cadastro.php">
        <div class="input-group">
            <label for="nome">Nome Completo</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
        </div>
        <div class="input-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="input-group">
            <label for="senha">Senha (min. 6 caracteres)</label>
            <input type="password" name="senha" required>
        </div>
        <div class="input-group">
            <label for="confirmar_senha">Confirmar Senha</label>
            <input type="password" name="confirmar_senha" required>
        </div>
        <button type="submit" class="btn-register">CADASTRAR</button>
    </form>
    
    <p class="login-link">Já tem conta? <a href="login.php">Faça login aqui</a></p>
</div>

</body>
</html>
