<?php
/**
 * Index: Relatórios
 * Redireciona para o dashboard de relatórios
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Auth.php';

// Verifica autenticação
Auth::requireLogin(BASE_URL);

// Redireciona para dashboard de relatórios
header('Location: dashboard.php');
exit;
