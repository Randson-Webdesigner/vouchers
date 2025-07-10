<?php
require_once 'includes/functions.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sucesso = '';
$erro = '';

if ($id <= 0) {
    header('Location: vouchers.php');
    exit();
}

$conexao = conectarDB();

// Buscar dados do voucher
$sql = "SELECT * FROM vouchers WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header('Location: vouchers.php');
    exit();
}

$voucher = $resultado->fetch_assoc();
$stmt->close();

// Processar baixa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($voucher['baixado'] == 'sim') {
        $erro = 'Este voucher já foi baixado anteriormente.';
    } else {
        $sql = "UPDATE vouchers SET baixado = 'sim', data_baixa = NOW() WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $sucesso = 'Voucher baixado com sucesso!';
            // Atualizar dados do voucher
            $voucher['baixado'] = 'sim';
            $voucher['data_baixa'] = date('Y-m-d H:i:s');
        } else {
            $erro = 'Erro ao dar baixa no voucher. Tente novamente.';
        }
        $stmt->close();
    }
}

$conexao->close();

$dias_restantes = calcularDiasRestantes($voucher['data_validade']);
$status = obterStatusVoucher($voucher['data_validade']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dar Baixa - Voucher <?php echo htmlspecialchars($voucher['codigo_voucher']); ?></title>
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
                <h1>Dar Baixa no Voucher</h1>
                <p>Código: <strong><?php echo htmlspecialchars($voucher['codigo_voucher']); ?></strong></p>
            </div>
            
            <?php if (!empty($sucesso)): ?>
                <div class="alert alert-success">
                    <?php echo $sucesso; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <div>
                    <h3>Dados do Voucher</h3>
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                        <div style="margin-bottom: 15px;">
                            <strong>Código:</strong> <?php echo htmlspecialchars($voucher['codigo_voucher']); ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Cliente:</strong> <?php echo htmlspecialchars($voucher['nome_cliente']); ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>CPF:</strong> <?php echo formatarCPF($voucher['cpf']); ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Número da Compra:</strong> <?php echo htmlspecialchars($voucher['numero_compra']); ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Desconto:</strong> 
                            <span style="color: #28a745; font-weight: bold;">
                                <?php echo number_format($voucher['valor_credito'], 2, ',', '.'); ?>%
                            </span>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Data de Criação:</strong> <?php echo date('d/m/Y H:i:s', strtotime($voucher['data_criacao'])); ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Data de Validade:</strong> <?php echo date('d/m/Y', strtotime($voucher['data_validade'])); ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Status:</strong> 
                            <?php if ($status == 'ativo'): ?>
                                <span class="status-badge status-ativo">Ativo</span>
                            <?php elseif ($status == 'expirado'): ?>
                                <span class="status-badge status-expirado">Expirado</span>
                            <?php else: ?>
                                <span class="status-badge status-proximo">Próximo da Expiração</span>
                            <?php endif; ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Baixado:</strong> 
                            <?php if ($voucher['baixado'] == 'sim'): ?>
                                <span class="status-badge status-expirado">Sim</span>
                                <br><small style="color: #666;">
                                    Data da baixa: <?php echo date('d/m/Y H:i:s', strtotime($voucher['data_baixa'])); ?>
                                </small>
                            <?php else: ?>
                                <span class="status-badge status-ativo">Não</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3>Ação de Baixa</h3>
                    <?php if ($voucher['baixado'] == 'sim'): ?>
                        <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; text-align: center;">
                            <h4 style="color: #721c24; margin-bottom: 15px;">⚠️ Voucher Já Baixado</h4>
                            <p style="color: #721c24; margin-bottom: 20px;">
                                Este voucher já foi utilizado e não pode ser baixado novamente.
                            </p>
                            <p style="color: #721c24; font-size: 14px;">
                                <strong>Data da baixa:</strong> <?php echo date('d/m/Y H:i:s', strtotime($voucher['data_baixa'])); ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px;">
                            <h4 style="color: #155724; margin-bottom: 15px;">✅ Confirmar Baixa</h4>
                            <p style="color: #155724; margin-bottom: 20px;">
                                Ao dar baixa neste voucher, você confirma que o desconto de 
                                <strong><?php echo number_format($voucher['valor_credito'], 2, ',', '.'); ?>%</strong> 
                                foi aplicado na compra.
                            </p>
                            
                            <form method="POST" action="" style="text-align: center;">
                                <button type="submit" class="btn btn-success" style="margin-right: 10px;">
                                    ✅ Confirmar Baixa
                                </button>
                                <a href="vouchers.php" class="btn btn-secondary">
                                    ❌ Cancelar
                                </a>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="vouchers.php" class="btn btn-primary">
                    ← Voltar à Lista
                </a>
                <a href="imprimir_voucher.php?id=<?php echo $voucher['id']; ?>&tipo=a4" class="btn btn-secondary" style="margin-left: 10px;">
                    📄 Imprimir Voucher
                </a>
            </div>
        </div>
    </div>
</body>
</html> 