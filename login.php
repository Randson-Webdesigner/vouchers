<?php
require_once 'config/database.php';

session_start();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];
    
    if (empty($usuario) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        $conexao = conectarDB();
        
        $sql = "SELECT id, usuario, senha FROM administradores WHERE usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows == 1) {
            $admin = $resultado->fetch_assoc();
            
            if (password_verify($senha, $admin['senha'])) {
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_usuario'] = $admin['usuario'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $erro = 'Senha incorreta.';
            }
        } else {
            $erro = 'Usuário não encontrado.';
        }
        
        $stmt->close();
        $conexao->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Vouchers</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 400px; margin: 100px auto;">
            <div class="card-header">
                <h1 style="text-align: center;">Sistema de Vouchers</h1>
                <p style="text-align: center; color: #666; margin-top: 10px;">Acesso Administrativo</p>
            </div>
            
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuário:</label>
                    <input type="text" id="usuario" name="usuario" class="form-control" 
                           value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Entrar
                </button>
            </form>
            
            <div style="margin-top: 20px; text-align: center; color: #666; font-size: 14px;">
                <p><strong>Credenciais padrão:</strong></p>
                <p>Usuário: admin</p>
                <p>Senha: admin123</p>
            </div>
        </div>
    </div>
</body>
</html> 