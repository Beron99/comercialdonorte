<?php
/**
 * Actions: Participantes
 * Processa ações relacionadas aos participantes
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações e classes
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/Participante.php';
require_once __DIR__ . '/../../app/controllers/ParticipanteController.php';

// Verifica autenticação
Auth::requireLogin(BASE_URL);

// Instancia controller
$controller = new ParticipanteController();

// Identifica ação
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;
$treinamentoId = $_GET['treinamento_id'] ?? $_POST['treinamento_id'] ?? 0;

// Valida treinamento
if (!$treinamentoId) {
    $_SESSION['error_message'] = 'Treinamento não informado';
    header('Location: ../treinamentos/listar.php');
    exit;
}

// Processa ações
switch ($action) {
    case 'checkin':
        // Verifica permissão
        if (!Auth::hasLevel(['admin', 'gestor', 'instrutor'])) {
            $_SESSION['error_message'] = 'Você não tem permissão para realizar check-in';
            header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
            exit;
        }

        if (!$id) {
            $_SESSION['error_message'] = 'Participante não informado';
            header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
            exit;
        }

        $resultado = $controller->processarCheckIn($id);

        if ($resultado['success']) {
            $_SESSION['success_message'] = $resultado['message'];
        } else {
            $_SESSION['error_message'] = $resultado['message'];
        }

        header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
        exit;

    case 'desvincular':
        // Verifica permissão
        if (!Auth::hasLevel(['admin', 'gestor'])) {
            $_SESSION['error_message'] = 'Você não tem permissão para desvincular participantes';
            header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
            exit;
        }

        if (!$id) {
            $_SESSION['error_message'] = 'Participante não informado';
            header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
            exit;
        }

        $resultado = $controller->processarDesvinculacao($id);

        if ($resultado['success']) {
            $_SESSION['success_message'] = $resultado['message'];
        } else {
            $_SESSION['error_message'] = $resultado['message'];
        }

        header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
        exit;

    case 'atualizar_status':
        // Verifica permissão
        if (!Auth::hasLevel(['admin', 'gestor', 'instrutor'])) {
            $_SESSION['error_message'] = 'Você não tem permissão para alterar status';
            header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
            exit;
        }

        if (!$id) {
            $_SESSION['error_message'] = 'Participante não informado';
            header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
            exit;
        }

        $status = $_POST['status'] ?? '';
        $resultado = $controller->processarAtualizacaoStatus($id, $status);

        if ($resultado['success']) {
            $_SESSION['success_message'] = $resultado['message'];
        } else {
            $_SESSION['error_message'] = $resultado['message'];
        }

        header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
        exit;

    case 'exportar':
        // Exporta para CSV
        $controller->exportarCSV($treinamentoId);
        exit;

    default:
        $_SESSION['error_message'] = 'Ação não reconhecida';
        header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
        exit;
}
