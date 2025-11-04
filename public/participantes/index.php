<?php
/**
 * Index: Participantes
 * Redireciona para a listagem de treinamentos para selecionar qual gerenciar
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Auth.php';

// Verifica autenticação
Auth::requireLogin(BASE_URL);

// Redireciona para listagem de treinamentos
$_SESSION['info_message'] = 'Selecione um treinamento para gerenciar os participantes';
header('Location: ../treinamentos/listar.php');
exit;
