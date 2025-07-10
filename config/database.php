<?php
// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'voucher_system');

// Conexão com o banco de dados
function conectarDB() {
    $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }
    
    $conexao->set_charset("utf8");
    return $conexao;
}

// Criar banco de dados e tabelas se não existirem
function criarBancoDados() {
    $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }
    
    // Criar banco de dados
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8 COLLATE utf8_unicode_ci";
    $conexao->query($sql);
    
    $conexao->select_db(DB_NAME);
    
    // Criar tabela de administradores
    $sql = "CREATE TABLE IF NOT EXISTS administradores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conexao->query($sql);
    
    // Criar tabela de vouchers
    $sql = "CREATE TABLE IF NOT EXISTS vouchers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome_cliente VARCHAR(100) NOT NULL,
        cpf VARCHAR(14) NOT NULL,
        numero_compra VARCHAR(50) NOT NULL,
        codigo_voucher VARCHAR(20) UNIQUE NOT NULL,
        valor_credito DECIMAL(10,2) DEFAULT 10.00,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_validade DATE NOT NULL,
        status ENUM('ativo', 'expirado') DEFAULT 'ativo',
        baixado ENUM('nao', 'sim') DEFAULT 'nao',
        data_baixa TIMESTAMP NULL
    )";
    $conexao->query($sql);
    
    // Inserir administrador padrão se não existir
    $sql = "SELECT COUNT(*) as total FROM administradores WHERE usuario = 'admin'";
    $resultado = $conexao->query($sql);
    $row = $resultado->fetch_assoc();
    
    if ($row['total'] == 0) {
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO administradores (usuario, senha) VALUES ('admin', '$senha_hash')";
        $conexao->query($sql);
    }
    
    $conexao->close();
}

// Inicializar banco de dados
criarBancoDados();
?> 