<?php
include 'confing.inc.php'; 

if (isset($conn) && !isset($conexao)) {
    $conexao = $conn;
}

if (!isset($conexao) || !$conexao) {
    die("ERRO FATAL: Não foi possível conectar ao banco de dados.");
}

$nome = ''; // Alterado de $nome_produto para $nome
$email = '';
$senha = '';
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nome = trim($_POST['nome_produto'] ?? ''); // Alterado de $nome_produto para $nome
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) { // Alterado de $nome_produto para $nome
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $sql_check = "SELECT id FROM usuarios WHERE email = ? AND senha = ?";
        $stmt_check = mysqli_prepare($conexao, $sql_check);
        $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt_check, "ss", $email, $senha_hashed);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        
        if (mysqli_num_rows($result_check) > 0) {
            $usuario = mysqli_fetch_assoc($result_check);
            $usuario_id = $usuario['id'];
            
            $sql_produto = "INSERT INTO produtos (usuario_id, nome) VALUES (?, ?)"; // Alterado para usar $nome
            $stmt_produto = mysqli_prepare($conexao, $sql_produto);
            mysqli_stmt_bind_param($stmt_produto, "is", $usuario_id, $nome); // Alterado para usar $nome
            
            if (mysqli_stmt_execute($stmt_produto)) {
                mysqli_stmt_close($stmt_produto);
                mysqli_stmt_close($stmt_check);
                header("Location: login.php?cadastro=sucesso");
                exit();
            } else {
                $erro = "Erro ao cadastrar produto: " . mysqli_error($conexao);
            }
        } else {
            $erro = "E-mail ou senha inválidos.";
        }
        mysqli_stmt_close($stmt_check);
    }
}

if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso') {
    $sucesso = "Produto cadastrado com sucesso!";
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
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .error-message { background-color: #ff6b6b; color: #fff; }
        .login-link { text-align: center; margin-top: 15px; }
        .login-link a { color: #2ecc71; text-decoration: none; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Cadastro de Produtos</h2>
    
    <?php if ($erro): ?>
        <p class="message error-message"><?php echo htmlspecialchars($erro); ?></p>
    <?php endif; ?>
    
    <?php if ($sucesso): ?>
        <p class="message" style="background-color: #2ecc71; color: #1a1a1a;"><?php echo htmlspecialchars($sucesso); ?></p>
    <?php endif; ?>
    
    <form method="POST" action="produtos_cadastro.php">
        <div class="input-group">
            <label for="nome_produto">Nome do Produto</label>
            <input type="text" id="nome_produto" name="nome_produto" value="<?php echo htmlspecialchars($nome); ?>" required>
        </div>
        <div class="input-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="input-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn-register">CADASTRAR</button>
    </form>
    
    <p class="login-link">Voltar para <a href="pag.php">página inicial</a></p>
</div>

</body>
</html>
