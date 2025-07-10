<?php
require_once 'config/database.php';

echo "<h2>Verificação do Banco de Dados</h2>";

try {
    $conexao = conectarDB();
    echo "<p style='color: green;'>✅ Conexão com o banco de dados estabelecida com sucesso!</p>";
    
    // Verificar tabela de administradores
    $sql = "SHOW TABLES LIKE 'administradores'";
    $resultado = $conexao->query($sql);
    if ($resultado->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabela 'administradores' criada com sucesso!</p>";
        
        // Verificar se existe o usuário admin
        $sql = "SELECT COUNT(*) as total FROM administradores WHERE usuario = 'admin'";
        $resultado = $conexao->query($sql);
        $row = $resultado->fetch_assoc();
        if ($row['total'] > 0) {
            echo "<p style='color: green;'>✅ Usuário administrador criado com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>❌ Usuário administrador não encontrado!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tabela 'administradores' não foi criada!</p>";
    }
    
    // Verificar tabela de vouchers
    $sql = "SHOW TABLES LIKE 'vouchers'";
    $resultado = $conexao->query($sql);
    if ($resultado->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabela 'vouchers' criada com sucesso!</p>";
        
        // Mostrar estrutura da tabela
        $sql = "DESCRIBE vouchers";
        $resultado = $conexao->query($sql);
        echo "<h3>Estrutura da tabela 'vouchers':</h3>";
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
    } else {
        echo "<p style='color: red;'>❌ Tabela 'vouchers' não foi criada!</p>";
    }
    
    $conexao->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<br><p><strong>Credenciais de acesso:</strong></p>";
echo "<p>Usuário: <strong>admin</strong></p>";
echo "<p>Senha: <strong>admin123</strong></p>";
echo "<br><p><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Acessar o Sistema</a></p>";
?> 