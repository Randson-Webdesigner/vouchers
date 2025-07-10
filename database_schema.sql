-- Sistema de Vouchers - Estrutura do Banco de Dados
-- Criado automaticamente pelo sistema

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS `voucher_system` 
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE `voucher_system`;

-- Tabela de administradores
CREATE TABLE IF NOT EXISTS `administradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL UNIQUE,
  `senha` varchar(255) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tabela de vouchers
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_cliente` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `numero_compra` varchar(50) NOT NULL,
  `codigo_voucher` varchar(20) NOT NULL UNIQUE,
  `valor_credito` decimal(10,2) NOT NULL DEFAULT 10.00,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_validade` date NOT NULL,
  `status` enum('ativo','expirado') NOT NULL DEFAULT 'ativo',
  `baixado` enum('nao','sim') NOT NULL DEFAULT 'nao',
  `data_baixa` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `idx_data_validade` (`data_validade`),
  KEY `idx_codigo_voucher` (`codigo_voucher`),
  KEY `idx_cpf` (`cpf`),
  KEY `idx_baixado` (`baixado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Inserir administrador padrão (senha: admin123)
INSERT INTO `administradores` (`usuario`, `senha`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE `usuario` = `usuario`;

-- Comentários sobre a estrutura:
-- 
-- Tabela 'administradores':
-- - id: Identificador único do administrador
-- - usuario: Nome de usuário único para login
-- - senha: Senha criptografada com password_hash()
-- - data_criacao: Data e hora de criação do registro
--
-- Tabela 'vouchers':
-- - id: Identificador único do voucher
-- - nome_cliente: Nome completo do cliente
-- - cpf: CPF do cliente (formato: 000.000.000-00)
-- - numero_compra: Número da compra/pedido
-- - codigo_voucher: Código único gerado automaticamente
-- - data_criacao: Data e hora de criação do voucher
-- - data_validade: Data de validade (60 dias após criação)
-- - status: Status do voucher (ativo/expirado)
--
-- Índices criados para otimização:
-- - idx_data_validade: Para consultas por data de validade
-- - idx_codigo_voucher: Para busca rápida por código
-- - idx_cpf: Para busca por CPF 