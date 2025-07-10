<?php
require_once 'config/database.php';

echo "<h2>Atualizando Estrutura do Banco de Dados</h2>";

try {
    $conexao = conectarDB();
    
    // Verificar se a coluna valor_credito já existe
    $sql = "SHOW COLUMNS FROM vouchers LIKE 'valor_credito'";
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows == 0) {
        // Adicionar coluna valor_credito
        $sql = "ALTER TABLE vouchers ADD COLUMN valor_credito DECIMAL(10,2) DEFAULT 10.00 AFTER codigo_voucher";
        if ($conexao->query($sql)) {
            echo "<p style='color: green;'>✅ Coluna 'valor_credito' adicionada com sucesso!</p>";
            
            // Atualizar registros existentes com valor padrão
            $sql = "UPDATE vouchers SET valor_credito = 10.00 WHERE valor_credito IS NULL";
            $conexao->query($sql);
            echo "<p style='color: green;'>✅ Registros existentes atualizados com valor padrão!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao adicionar coluna: " . $conexao->error . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Coluna 'valor_credito' já existe!</p>";
    }
    
    // Verificar se a coluna baixado já existe
    $sql = "SHOW COLUMNS FROM vouchers LIKE 'baixado'";
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows == 0) {
        // Adicionar colunas de baixa
        $sql = "ALTER TABLE vouchers ADD COLUMN baixado ENUM('nao', 'sim') DEFAULT 'nao' AFTER status";
        if ($conexao->query($sql)) {
            echo "<p style='color: green;'>✅ Coluna 'baixado' adicionada com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao adicionar coluna baixado: " . $conexao->error . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Coluna 'baixado' já existe!</p>";
    }
    
    // Verificar se a coluna data_baixa já existe
    $sql = "SHOW COLUMNS FROM vouchers LIKE 'data_baixa'";
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows == 0) {
        // Adicionar coluna data_baixa
        $sql = "ALTER TABLE vouchers ADD COLUMN data_baixa TIMESTAMP NULL AFTER baixado";
        if ($conexao->query($sql)) {
            echo "<p style='color: green;'>✅ Coluna 'data_baixa' adicionada com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao adicionar coluna data_baixa: " . $conexao->error . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Coluna 'data_baixa' já existe!</p>";
    }
    
    // Verificar estrutura final
    $sql = "DESCRIBE vouchers";
    $resultado = $conexao->query($sql);
    echo "<h3>Estrutura atual da tabela 'vouchers':</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>";
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $conexao->close();
    echo "<br><p style='color: green;'><strong>✅ Atualização concluída com sucesso!</strong></p>";
    echo "<p><a href='vouchers.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ver Vouchers</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?> 