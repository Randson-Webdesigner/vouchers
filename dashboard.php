<?php
require_once 'includes/functions.php';
verificarLogin();

$conexao = conectarDB();

// Buscar estatísticas
$sql = "SELECT 
            COUNT(*) as total_vouchers,
            SUM(CASE WHEN data_validade >= CURDATE() THEN 1 ELSE 0 END) as vouchers_ativos,
            SUM(CASE WHEN data_validade < CURDATE() THEN 1 ELSE 0 END) as vouchers_expirados,
            SUM(CASE WHEN data_validade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as proximos_expiracao,
            SUM(CASE WHEN baixado = 'sim' THEN 1 ELSE 0 END) as vouchers_baixados,
            SUM(CASE WHEN baixado = 'nao' THEN 1 ELSE 0 END) as vouchers_nao_baixados
        FROM vouchers";
$resultado = $conexao->query($sql);
$stats = $resultado->fetch_assoc();

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Vouchers</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">Sistema de Vouchers</a>
            <ul class="navbar-nav">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="gerar_voucher.php">Gerar Voucher</a></li>
                <li><a href="vouchers.php">Visualizar Vouchers</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Dashboard</h1>
                <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['admin_usuario']); ?>!</p>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_vouchers']; ?></div>
                    <div class="stat-label">Total de Vouchers</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['vouchers_ativos']; ?></div>
                    <div class="stat-label">Vouchers Ativos</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['vouchers_expirados']; ?></div>
                    <div class="stat-label">Vouchers Expirados</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['proximos_expiracao']; ?></div>
                    <div class="stat-label">Próximos da Expiração</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['vouchers_baixados']; ?></div>
                    <div class="stat-label">Vouchers Baixados</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['vouchers_nao_baixados']; ?></div>
                    <div class="stat-label">Não Baixados</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="gerar_voucher.php" class="btn btn-primary" style="margin-right: 15px;">
                    Gerar Novo Voucher
                </a>
                <a href="vouchers.php" class="btn btn-success">
                    Visualizar Todos os Vouchers
                </a>
            </div>
        </div>
    </div>
</body>
</html> 