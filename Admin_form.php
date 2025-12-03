<?php
session_start();
include 'public/confing.inc.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Preencha e-mail e senha.";
    } else {
        $sql = "SELECT id, nome, senha FROM usuarios WHERE email = '$email' AND is_admin = 1";
        $resultado = mysqli_query($conn, $sql);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $admin = mysqli_fetch_assoc($resultado);

            if (password_verify($senha, $admin['senha'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nome'] = $admin['nome'];

                header("Location: admin/dashboard.php");
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
    <title>Login Admin | Mundo Gamer Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin/assets/styles.css">
</head>
<body>

<div class="login-container">
    <h2>Acessar Conta Admin</h2>

    <?php if ($erro): ?>
        <div class="message error-message"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST" action="Admin_form.php">
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
</div>

</body>
</html>