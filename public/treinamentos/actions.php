<?php
/**
 * Actions: Processa ações dos treinamentos
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações e classes
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/Treinamento.php';
require_once __DIR__ . '/../../app/controllers/TreinamentoController.php';

// Verifica autenticação
Auth::requireLogin(BASE_URL);

// Instancia controller
$controller = new TreinamentoController();

// Processa ação
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

// Ação de exportar CSV (não precisa de ID)
if ($action === 'exportar') {
    // Pega filtros da URL
    $params = [
        'search' => $_GET['search'] ?? '',
        'tipo' => $_GET['tipo'] ?? '',
        'status' => $_GET['status'] ?? '',
        'ano' => $_GET['ano'] ?? ''
    ];

    // Remove parâmetro 'action'
    unset($_GET['action']);

    // Exporta e termina execução
    $controller->exportarCSV($params);
    exit;
}

// Valida ação e ID
if (empty($action) || empty($id)) {
    $_SESSION['error_message'] = 'Ação ou ID inválido';
    header('Location: listar.php');
    exit;
}

switch ($action) {
    case 'cancelar':
        // Verifica permissão (apenas admin e gestor podem cancelar)
        if (!Auth::hasLevel(['admin', 'gestor'])) {
            $_SESSION['error_message'] = 'Você não tem permissão para cancelar treinamentos';
            header('Location: listar.php');
            exit;
        }
        $resultado = $controller->cancelar($id);
        break;

    case 'marcar_executado':
        // Verifica permissão (apenas admin e gestor podem marcar como executado)
        if (!Auth::hasLevel(['admin', 'gestor'])) {
            $_SESSION['error_message'] = 'Você não tem permissão para marcar treinamentos como executados';
            header('Location: listar.php');
            exit;
        }
        $resultado = $controller->marcarExecutado($id);
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

header('Location: visualizar.php?id=' . $id);
exit;
