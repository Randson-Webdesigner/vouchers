<?php
require_once 'config/database.php';

// Função para gerar código único do voucher
function gerarCodigoVoucher() {
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $codigo = '';
    
    for ($i = 0; $i < 8; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    
    return $codigo;
}

// Função para calcular dias restantes
function calcularDiasRestantes($data_validade) {
    $hoje = new DateTime();
    $validade = new DateTime($data_validade);
    $diferenca = $hoje->diff($validade);
    
    if ($hoje > $validade) {
        return -$diferenca->days; // Negativo para indicar expirado
    }
    
    return $diferenca->days;
}

// Função para obter status do voucher
function obterStatusVoucher($data_validade) {
    $dias_restantes = calcularDiasRestantes($data_validade);
    
    if ($dias_restantes < 0) {
        return 'expirado';
    } elseif ($dias_restantes <= 7) {
        return 'proximo_expiracao';
    } else {
        return 'ativo';
    }
}

// Função para formatar CPF
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

// Função para validar CPF
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return false;
    }
    
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

// Função para verificar se usuário está logado
function verificarLogin() {
    session_start();
    if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
        header('Location: login.php');
        exit();
    }
}

// Função para fazer logout
function fazerLogout() {
    session_start();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Função para formatar valor monetário
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

// Função para gerar código de barras (simples)
function gerarCodigoBarras($codigo) {
    return $codigo; // Implementação básica - pode ser expandida para códigos de barras reais
}
?> 