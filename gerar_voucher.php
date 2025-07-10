<?php
require_once 'includes/functions.php';
verificarLogin();

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_cliente = trim($_POST['nome_cliente']);
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
    $numero_compra = trim($_POST['numero_compra']);
    $valor_credito = isset($_POST['valor_credito']) ? (float)$_POST['valor_credito'] : 10.00;
    
    // Validações
    if (empty($nome_cliente) || empty($cpf) || empty($numero_compra)) {
        $erro = 'Por favor, preencha todos os campos.';
    } elseif (!validarCPF($cpf)) {
        $erro = 'CPF inválido.';
    } elseif ($valor_credito <= 0) {
        $erro = 'A porcentagem do desconto deve ser maior que zero.';
    } elseif ($valor_credito > 100) {
        $erro = 'A porcentagem do desconto não pode ser maior que 100%.';
    } else {
        $conexao = conectarDB();
        
        // Gerar código único do voucher
        do {
            $codigo_voucher = gerarCodigoVoucher();
            $sql = "SELECT COUNT(*) as total FROM vouchers WHERE codigo_voucher = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("s", $codigo_voucher);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();
        } while ($row['total'] > 0);
        
        // Calcular data de validade (60 dias)
        $data_validade = date('Y-m-d', strtotime('+60 days'));
        
        // Inserir voucher
        $sql = "INSERT INTO vouchers (nome_cliente, cpf, numero_compra, codigo_voucher, valor_credito, data_validade) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssds", $nome_cliente, $cpf, $numero_compra, $codigo_voucher, $valor_credito, $data_validade);
        
        if ($stmt->execute()) {
            $sucesso = "Voucher gerado com sucesso! Código: <strong>$codigo_voucher</strong> - Desconto: <strong>" . number_format($valor_credito, 2, ',', '.') . "%</strong>";
            // Limpar formulário
            $_POST = array();
        } else {
            $erro = 'Erro ao gerar voucher. Tente novamente.';
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
    <title>Gerar Voucher - Sistema de Vouchers</title>
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
                <h1>Gerar Novo Voucher</h1>
                <p>Preencha os dados do cliente para gerar um novo voucher</p>
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
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome_cliente">Nome do Cliente:</label>
                    <input type="text" id="nome_cliente" name="nome_cliente" class="form-control" 
                           value="<?php echo isset($_POST['nome_cliente']) ? htmlspecialchars($_POST['nome_cliente']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" 
                           value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" 
                           placeholder="000.000.000-00" required>
                </div>
                
                <div class="form-group">
                    <label for="numero_compra">Número da Compra:</label>
                    <input type="text" id="numero_compra" name="numero_compra" class="form-control" 
                           value="<?php echo isset($_POST['numero_compra']) ? htmlspecialchars($_POST['numero_compra']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="valor_credito">Porcentagem de Desconto (%):</label>
                    <input type="number" id="valor_credito" name="valor_credito" class="form-control" 
                           value="<?php echo isset($_POST['valor_credito']) ? htmlspecialchars($_POST['valor_credito']) : '10'; ?>" 
                           step="0.01" min="0.01" max="100.00" required>
                    <small style="color: #666;">Digite a porcentagem de desconto que será aplicada (ex: 10 para 10%)</small>
                </div>
                
                <div class="form-group">
                    <div id="porcentagem_info" style="display: none; background: #e7f3ff; padding: 10px; border-radius: 5px; border-left: 4px solid #007bff;">
                        <strong>Porcentagem do Desconto:</strong> <span id="porcentagem_valor">0%</span>
                    </div>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px; color: #495057;">Informações do Voucher:</h4>
                    <ul style="margin: 0; padding-left: 20px; color: #666;">
                        <li>Validade: 60 dias a partir da data de criação</li>
                        <li>Código do voucher será gerado automaticamente</li>
                        <li>O voucher será marcado como ativo</li>
                        <li>O desconto será aplicado em porcentagem (%) conforme informado acima</li>
                    </ul>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Gerar Voucher
                </button>
                
                <a href="dashboard.php" class="btn btn-secondary" style="margin-left: 10px;">
                    Voltar
                </a>
            </form>
        </div>
    </div>

    <script>
        // Máscara para CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                e.target.value = value;
            }
        });
        
        // Calcular porcentagem do desconto
        function calcularPorcentagem() {
            const valorCredito = parseFloat(document.getElementById('valor_credito').value) || 0;
            const valorCompra = parseFloat(document.getElementById('valor_compra').value) || 0;
            
            if (valorCompra > 0 && valorCredito > 0) {
                const porcentagem = (valorCredito / valorCompra) * 100;
                document.getElementById('porcentagem_valor').textContent = porcentagem.toFixed(2) + '%';
                document.getElementById('porcentagem_info').style.display = 'block';
            } else {
                document.getElementById('porcentagem_info').style.display = 'none';
            }
        }
        
        // Event listeners para calcular porcentagem
        document.getElementById('valor_credito').addEventListener('input', calcularPorcentagem);
        document.getElementById('valor_compra').addEventListener('input', calcularPorcentagem);
    </script>
</body>
</html> 