<?php
require_once 'includes/functions.php';
verificarLogin();

$conexao = conectarDB();

// Filtros
$filtro_status = isset($_GET['status']) ? $_GET['status'] : '';
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Construir query
$sql = "SELECT * FROM vouchers WHERE 1=1";
$params = array();
$types = "";

if (!empty($filtro_status)) {
    if ($filtro_status == 'ativo') {
        $sql .= " AND data_validade >= CURDATE()";
    } elseif ($filtro_status == 'expirado') {
        $sql .= " AND data_validade < CURDATE()";
    } elseif ($filtro_status == 'proximo_expiracao') {
        $sql .= " AND data_validade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    }
}

if (!empty($busca)) {
    $sql .= " AND (nome_cliente LIKE ? OR cpf LIKE ? OR numero_compra LIKE ? OR codigo_voucher LIKE ?)";
    $busca_param = "%$busca%";
    $params[] = $busca_param;
    $params[] = $busca_param;
    $params[] = $busca_param;
    $params[] = $busca_param;
    $types .= "ssss";
}

$sql .= " ORDER BY data_criacao DESC";

$stmt = $conexao->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

$vouchers = array();
while ($row = $resultado->fetch_assoc()) {
    $vouchers[] = $row;
}

$stmt->close();
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Vouchers - Sistema de Vouchers</title>
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

    <div class="container-full">
        <div class="card">
            <div class="card-header">
                <h1>Visualizar Vouchers</h1>
                <p>Gerencie e visualize todos os vouchers do sistema</p>
            </div>
            
            <!-- Filtros e Busca -->
            <form method="GET" action="" class="search-box">
                <input type="text" name="busca" class="form-control" 
                       placeholder="Buscar por nome, CPF, número da compra ou código do voucher"
                       value="<?php echo htmlspecialchars($busca); ?>">
                
                <select name="status" class="form-control" style="width: 200px;">
                    <option value="">Todos os Status</option>
                    <option value="ativo" <?php echo $filtro_status == 'ativo' ? 'selected' : ''; ?>>Ativos</option>
                    <option value="expirado" <?php echo $filtro_status == 'expirado' ? 'selected' : ''; ?>>Expirados</option>
                    <option value="proximo_expiracao" <?php echo $filtro_status == 'proximo_expiracao' ? 'selected' : ''; ?>>Próximos da Expiração</option>
                </select>
                
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="vouchers.php" class="btn btn-secondary">Limpar</a>
            </form>
            
            <!-- Tabela de Vouchers -->
            <?php if (empty($vouchers)): ?>
                <div class="alert alert-warning">
                    Nenhum voucher encontrado com os filtros aplicados.
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>CPF</th>
                                <th>Nº Compra</th>
                                <th>Crédito</th>
                                <th>Data Criação</th>
                                <th>Data Validade</th>
                                <th>Dias Restantes</th>
                                <th>Status</th>
                                <th>Baixa</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vouchers as $voucher): ?>
                                <?php 
                                $dias_restantes = calcularDiasRestantes($voucher['data_validade']);
                                $status = obterStatusVoucher($voucher['data_validade']);
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($voucher['codigo_voucher']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($voucher['nome_cliente']); ?></td>
                                    <td><?php echo formatarCPF($voucher['cpf']); ?></td>
                                    <td><?php echo htmlspecialchars($voucher['numero_compra']); ?></td>
                                    <td>
                                        <strong style="color: #28a745;">
                                            <?php echo number_format($voucher['valor_credito'], 2, ',', '.'); ?>%
                                        </strong>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($voucher['data_criacao'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($voucher['data_validade'])); ?></td>
                                    <td>
                                        <?php if ($dias_restantes >= 0): ?>
                                            <span style="color: #28a745; font-weight: 600;">
                                                <?php echo $dias_restantes; ?> dias
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #dc3545; font-weight: 600;">
                                                Expirado há <?php echo abs($dias_restantes); ?> dias
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($status == 'ativo'): ?>
                                            <span class="status-badge status-ativo">Ativo</span>
                                        <?php elseif ($status == 'expirado'): ?>
                                            <span class="status-badge status-expirado">Expirado</span>
                                        <?php else: ?>
                                            <span class="status-badge status-proximo">Próximo da Expiração</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($voucher['baixado']) && $voucher['baixado'] == 'sim'): ?>
                                            <span class="status-badge status-expirado">Baixado</span>
                                            <br><small style="color: #666; font-size: 10px;">
                                                <?php echo date('d/m/Y', strtotime($voucher['data_baixa'])); ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="status-badge status-ativo">Não Baixado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="imprimir_voucher.php?id=<?php echo $voucher['id']; ?>&tipo=termica" 
                                           class="btn btn-primary" style="padding: 5px 10px; font-size: 12px; margin-right: 5px;">
                                            🖨️ Termica
                                        </a>
                                        <a href="imprimir_voucher.php?id=<?php echo $voucher['id']; ?>&tipo=a4" 
                                           class="btn btn-success" style="padding: 5px 10px; font-size: 12px; margin-right: 5px;">
                                            📄 A4
                                        </a>
                                        <?php if (!isset($voucher['baixado']) || $voucher['baixado'] == 'nao'): ?>
                                            <a href="dar_baixa.php?id=<?php echo $voucher['id']; ?>" 
                                               class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">
                                                ✅ Baixa
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 20px; text-align: center;">
                    <p style="color: #666;">
                        Total de vouchers encontrados: <strong><?php echo count($vouchers); ?></strong>
                    </p>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px; text-align: center;">
                <a href="gerar_voucher.php" class="btn btn-primary">
                    Gerar Novo Voucher
                </a>
                <a href="dashboard.php" class="btn btn-secondary" style="margin-left: 10px;">
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html> 