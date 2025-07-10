# Sistema de Vouchers em PHP

Um sistema completo para gerenciamento de vouchers com interface administrativa, desenvolvido em PHP e MySQL.

## Funcionalidades

- ✅ **Sistema de Login Administrativo**
  - Autenticação segura com hash de senha
  - Sessões protegidas
  - Credenciais padrão configuradas

- ✅ **Geração de Vouchers**
  - Formulário completo com validação
  - Validação de CPF
  - Geração automática de código único
  - Validade de 60 dias automática

- ✅ **Visualização de Vouchers**
  - Lista completa de todos os vouchers
  - Filtros por status (Ativo, Expirado, Próximo da Expiração)
  - Busca por nome, CPF, número da compra ou código
  - Cálculo automático de dias restantes

- ✅ **Dashboard com Estatísticas**
  - Total de vouchers
  - Vouchers ativos
  - Vouchers expirados
  - Vouchers próximos da expiração

- ✅ **Status Inteligente**
  - Ativo: Voucher válido
  - Próximo da Expiração: 7 dias ou menos
  - Expirado: Data de validade vencida

- ✅ **Sistema de Impressão**
  - Impressão para impressora térmica (80mm)
  - Impressão em formato A4
  - Layout otimizado para cada tipo de impressão
  - Código de barras simulado

- ✅ **Sistema de Crédito**
  - Crédito de R$ 10,00 (10% de desconto)
  - Exibição do valor em todas as telas
  - Formatação monetária adequada

- ✅ **Sistema de Baixa**
  - Controle de vouchers utilizados
  - Data e hora da baixa registrada
  - Prevenção de uso duplo
  - Estatísticas de baixa no dashboard

## Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: mysqli, session

## Instalação

### 1. Configuração do Banco de Dados

1. Certifique-se de que o MySQL está rodando
2. O sistema criará automaticamente:
   - Banco de dados: `voucher_system`
   - Tabelas: `administradores`, `vouchers`
   - Usuário administrador padrão

### 2. Configuração do PHP

1. Coloque os arquivos na pasta do seu servidor web (ex: `htdocs/voucher/`)
2. Verifique se as extensões PHP necessárias estão habilitadas
3. Certifique-se de que o PHP tem permissão para conectar ao MySQL

### 3. Configuração do Banco (se necessário)

Edite o arquivo `config/database.php` se suas credenciais do MySQL forem diferentes:

```php
define('DB_HOST', 'localhost');  // Host do MySQL
define('DB_USER', 'root');       // Usuário do MySQL
define('DB_PASS', '');           // Senha do MySQL
define('DB_NAME', 'voucher_system'); // Nome do banco
```

## Primeiro Acesso

1. Acesse o sistema através do navegador
2. Use as credenciais padrão:
   - **Usuário:** `admin`
   - **Senha:** `admin123`
3. Recomenda-se alterar a senha após o primeiro acesso

## Estrutura do Projeto

```
voucher/
├── config/
│   └── database.php          # Configuração do banco de dados
├── includes/
│   └── functions.php         # Funções auxiliares
├── assets/
│   └── css/
│       └── style.css         # Estilos do sistema
├── index.php                 # Redirecionamento para login
├── login.php                 # Página de login
├── dashboard.php             # Dashboard principal
├── gerar_voucher.php         # Geração de vouchers
├── vouchers.php              # Visualização de vouchers
├── imprimir_voucher.php      # Impressão de vouchers
├── dar_baixa.php             # Sistema de baixa de vouchers
├── logout.php                # Logout do sistema
├── verificar_banco.php       # Verificação do banco
├── atualizar_banco.php       # Atualização da estrutura
├── database_schema.sql       # Schema do banco
└── README.md                 # Este arquivo
```

## Como Usar

### 1. Login
- Acesse `http://localhost/voucher/`
- Faça login com as credenciais administrativas

### 2. Gerar Voucher
- Clique em "Gerar Voucher" no menu
- Preencha os dados do cliente:
  - Nome completo
  - CPF (com validação automática)
  - Número da compra
- O sistema gerará automaticamente:
  - Código único do voucher
  - Data de validade (60 dias)

### 3. Visualizar Vouchers
- Clique em "Visualizar Vouchers" no menu
- Use os filtros para encontrar vouchers específicos
- Veja o status de cada voucher:
  - **Verde:** Ativo
  - **Amarelo:** Próximo da expiração
  - **Vermelho:** Expirado
- Visualize o valor do crédito de cada voucher
- Use os botões de impressão para imprimir vouchers

### 4. Imprimir Vouchers
- Clique nos botões "🖨️ Termica" ou "📄 A4" na lista de vouchers
- **Impressora Térmica:** Formato otimizado para 80mm
- **Formato A4:** Layout completo com instruções
- Ambos incluem código de barras e valor do crédito

### 5. Dar Baixa em Vouchers
- Clique no botão "✅ Baixa" na lista de vouchers
- Confirme a baixa do voucher
- Sistema registra data e hora da baixa
- Vouchers baixados não podem ser utilizados novamente

### 6. Dashboard
- Visualize estatísticas em tempo real
- Acompanhe o status geral dos vouchers

## Segurança

- Senhas criptografadas com `password_hash()`
- Proteção contra SQL Injection com prepared statements
- Validação de entrada de dados
- Sessões seguras
- Escape de saída HTML

## Personalização

### Alterar Validade dos Vouchers
Edite o arquivo `gerar_voucher.php`, linha 35:
```php
$data_validade = date('Y-m-d', strtotime('+60 days')); // Altere 60 para o número de dias desejado
```

### Alterar Cores e Estilo
Edite o arquivo `assets/css/style.css` para personalizar a aparência.

### Adicionar Novos Campos
1. Adicione as colunas na tabela `vouchers`
2. Atualize os formulários em `gerar_voucher.php`
3. Atualize a exibição em `vouchers.php`

## Suporte

Para dúvidas ou problemas:
1. Verifique se o MySQL está rodando
2. Confirme as credenciais do banco em `config/database.php`
3. Verifique os logs de erro do PHP
4. Certifique-se de que todas as extensões PHP estão habilitadas

## Licença

Este projeto é de uso livre para fins educacionais e comerciais. 