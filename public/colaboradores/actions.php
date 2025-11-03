<?php
/**
 * Actions: Processa ações dos colaboradores
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações e classes
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/Colaborador.php';
require_once __DIR__ . '/../../app/controllers/ColaboradorController.php';

// Verifica autenticação
Auth::requireLogin(BASE_URL);

// Instancia controller
$controller = new ColaboradorController();

// Processa ação
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

if (empty($action) || empty($id)) {
    $_SESSION['error_message'] = 'Ação ou ID inválido';
    header('Location: listar.php');
    exit;
}

switch ($action) {
    case 'inativar':
        $resultado = $controller->inativar($id);
        break;

    case 'ativar':
        $resultado = $controller->ativar($id);
        break;

    default:
        $_SESSION['error_message'] = 'Ação desconhecida';
        header('Location: listar.php');
        exit;
}

// Redireciona com mensagem
if ($resultado['success']) {
    $_SESSION['success_message'] = $resultado['message'];
} else {
    $_SESSION['error_message'] = $resultado['message'];
}

header('Location: listar.php');
exit;
