<?php
/**
 * Página de Logout
 * Encerra sessão do usuário
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/classes/Database.php';
require_once __DIR__ . '/../app/classes/Auth.php';

// Realiza logout
$auth = new Auth();
$auth->logout();

// Verifica se foi timeout
$timeout = isset($_GET['timeout']) && $_GET['timeout'] == 1;

// Redireciona para login com mensagem
if ($timeout) {
    $_SESSION['warning_message'] = 'Sua sessão expirou por inatividade. Faça login novamente.';
} else {
    $_SESSION['success_message'] = 'Logout realizado com sucesso!';
}

header('Location: index.php');
exit;
