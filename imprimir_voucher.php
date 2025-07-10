<?php
require_once 'includes/functions.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'a4';

if ($id <= 0) {
    header('Location: vouchers.php');
    exit();
}

$conexao = conectarDB();
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
$conexao->close();

$dias_restantes = calcularDiasRestantes($voucher['data_validade']);
$status = obterStatusVoucher($voucher['data_validade']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Voucher - <?php echo htmlspecialchars($voucher['codigo_voucher']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
        }
        
        .voucher-termica {
            width: 80mm;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0 auto;
            background: white;
            padding: 10px;
        }
        
        .voucher-a4 {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        
        .header-termica {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .header-a4 {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .content-termica {
            margin-bottom: 15px;
        }
        
        .content-a4 {
            margin-bottom: 30px;
        }
        
        .field-termica {
            margin-bottom: 5px;
        }
        
        .field-a4 {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .label-termica {
            font-weight: bold;
        }
        
        .label-a4 {
            font-weight: bold;
            color: #333;
            min-width: 150px;
        }
        
        .value-termica {
            margin-left: 10px;
        }
        
        .value-a4 {
            text-align: right;
            flex: 1;
        }
        
        .footer-termica {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .footer-a4 {
            text-align: center;
            border-top: 2px solid #333;
            padding-top: 20px;
            margin-top: 50px;
            font-size: 14px;
        }
        
        .codigo-barras {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }
        
        .valor-destaque {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #d4edda;
            border: 2px solid #28a745;
            border-radius: 8px;
        }
        
        .status-badge-print {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-ativo-print {
            background: #d4edda;
            color: #155724;
        }
        
        .status-expirado-print {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-proximo-print {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; padding: 20px;">
        <h2>Imprimir Voucher</h2>
        <p>Voucher: <strong><?php echo htmlspecialchars($voucher['codigo_voucher']); ?></strong></p>
        <p>Tipo de Impressão: <strong><?php echo strtoupper($tipo); ?></strong></p>
        <button onclick="window.print()" class="btn btn-primary" style="margin-right: 10px;">
            🖨️ Imprimir
        </button>
        <a href="vouchers.php" class="btn btn-secondary">
            ← Voltar
        </a>
    </div>

    <?php if ($tipo == 'termica'): ?>
        <!-- Voucher para Impressora Térmica -->
        <div class="voucher-termica">
            <div class="header-termica">
                <div style="font-size: 16px; font-weight: bold;">VOUCHER DE DESCONTO</div>
                <div style="font-size: 12px;">Sistema de Vouchers</div>
                <div style="font-size: 10px;"><?php echo date('d/m/Y H:i:s'); ?></div>
            </div>
            
            <div class="content-termica">
                <div class="field-termica">
                    <span class="label-termica">Código:</span>
                    <span class="value-termica"><?php echo htmlspecialchars($voucher['codigo_voucher']); ?></span>
                </div>
                
                <div class="field-termica">
                    <span class="label-termica">Cliente:</span>
                    <span class="value-termica"><?php echo htmlspecialchars($voucher['nome_cliente']); ?></span>
                </div>
                
                <div class="field-termica">
                    <span class="label-termica">CPF:</span>
                    <span class="value-termica"><?php echo formatarCPF($voucher['cpf']); ?></span>
                </div>
                
                <div class="field-termica">
                    <span class="label-termica">Compra:</span>
                    <span class="value-termica"><?php echo htmlspecialchars($voucher['numero_compra']); ?></span>
                </div>
                
                <div class="field-termica">
                    <span class="label-termica">Validade:</span>
                    <span class="value-termica"><?php echo date('d/m/Y', strtotime($voucher['data_validade'])); ?></span>
                </div>
                
                <div class="field-termica">
                    <span class="label-termica">Status:</span>
                    <span class="value-termica">
                        <?php if ($status == 'ativo'): ?>
                            <span class="status-badge-print status-ativo-print">Ativo</span>
                        <?php elseif ($status == 'expirado'): ?>
                            <span class="status-badge-print status-expirado-print">Expirado</span>
                        <?php else: ?>
                            <span class="status-badge-print status-proximo-print">Próximo</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <div style="text-align: center; margin: 20px 0; font-size: 18px; font-weight: bold;">
                CRÉDITO DE DESCONTO
            </div>
            
            <div style="text-align: center; font-size: 24px; font-weight: bold; color: #28a745; margin: 15px 0;">
                <?php echo number_format($voucher['valor_credito'], 2, ',', '.'); ?>%
            </div>
            
            <div style="text-align: center; font-size: 10px; margin: 10px 0;">
                (Desconto personalizado)
            </div>
            
            <div class="codigo-barras">
                *<?php echo htmlspecialchars($voucher['codigo_voucher']); ?>*
            </div>
            
            <div class="footer-termica">
                <div>Voucher válido até <?php echo date('d/m/Y', strtotime($voucher['data_validade'])); ?></div>
                <div>Apresente este voucher no momento da compra</div>
                <div>Não é cumulativo com outras promoções</div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Voucher para Impressão A4 -->
        <div class="voucher-a4">
            <div class="header-a4">
                <h1 style="color: #333; margin-bottom: 10px;">VOUCHER DE DESCONTO</h1>
                <h3 style="color: #666; margin-bottom: 5px;">Sistema de Vouchers</h3>
                <p style="color: #999;">Gerado em: <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
            
            <div class="content-a4">
                <div class="field-a4">
                    <span class="label-a4">Código do Voucher:</span>
                    <span class="value-a4" style="font-size: 18px; font-weight: bold; color: #007bff;">
                        <?php echo htmlspecialchars($voucher['codigo_voucher']); ?>
                    </span>
                </div>
                
                <div class="field-a4">
                    <span class="label-a4">Nome do Cliente:</span>
                    <span class="value-a4"><?php echo htmlspecialchars($voucher['nome_cliente']); ?></span>
                </div>
                
                <div class="field-a4">
                    <span class="label-a4">CPF:</span>
                    <span class="value-a4"><?php echo formatarCPF($voucher['cpf']); ?></span>
                </div>
                
                <div class="field-a4">
                    <span class="label-a4">Número da Compra:</span>
                    <span class="value-a4"><?php echo htmlspecialchars($voucher['numero_compra']); ?></span>
                </div>
                
                <div class="field-a4">
                    <span class="label-a4">Data de Criação:</span>
                    <span class="value-a4"><?php echo date('d/m/Y H:i:s', strtotime($voucher['data_criacao'])); ?></span>
                </div>
                
                <div class="field-a4">
                    <span class="label-a4">Data de Validade:</span>
                    <span class="value-a4"><?php echo date('d/m/Y', strtotime($voucher['data_validade'])); ?></span>
                </div>
                
                <div class="field-a4">
                    <span class="label-a4">Dias Restantes:</span>
                    <span class="value-a4">
                        <?php if ($dias_restantes >= 0): ?>
                            <span style="color: #28a745; font-weight: 600;">
                                <?php echo $dias_restantes; ?> dias
                            </span>
                        <?php else: ?>
                            <span style="color: #dc3545; font-weight: 600;">
                                Expirado há <?php echo abs($dias_restantes); ?> dias
                            </span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="field-a4">
                    <span class="label-a4">Status:</span>
                    <span class="value-a4">
                        <?php if ($status == 'ativo'): ?>
                            <span class="status-badge-print status-ativo-print">Ativo</span>
                        <?php elseif ($status == 'expirado'): ?>
                            <span class="status-badge-print status-expirado-print">Expirado</span>
                        <?php else: ?>
                            <span class="status-badge-print status-proximo-print">Próximo da Expiração</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <div class="valor-destaque">
                CRÉDITO DE DESCONTO: <?php echo number_format($voucher['valor_credito'], 2, ',', '.'); ?>%
                <div style="font-size: 16px; margin-top: 10px;">(Desconto personalizado)</div>
            </div>
            
            <div class="codigo-barras">
                Código de Barras: <?php echo htmlspecialchars($voucher['codigo_voucher']); ?>
            </div>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0;">
                <h4 style="color: #333; margin-bottom: 15px;">Instruções de Uso:</h4>
                <ul style="color: #666; line-height: 1.6;">
                    <li>Apresente este voucher no momento da compra</li>
                    <li>O desconto será aplicado automaticamente</li>
                    <li>Voucher válido até a data de validade</li>
                    <li>Não é cumulativo com outras promoções</li>
                    <li>Voucher de uso único</li>
                </ul>
            </div>
            
            <div class="footer-a4">
                <p><strong>Sistema de Vouchers</strong></p>
                <p>Voucher válido até <?php echo date('d/m/Y', strtotime($voucher['data_validade'])); ?></p>
                <p style="font-size: 12px; color: #666;">Este documento é um comprovante oficial de desconto</p>
            </div>
        </div>
    <?php endif; ?>
</body>
</html> 