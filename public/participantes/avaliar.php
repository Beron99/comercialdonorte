<?php
/**
 * View: Avaliar Participante
 * Formulário para registrar avaliação de reação, aprendizado e comportamento
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

// Verifica ID do participante
$participanteId = $_GET['id'] ?? 0;
$treinamentoId = $_GET['treinamento_id'] ?? 0;

if (!$participanteId || !$treinamentoId) {
    $_SESSION['error_message'] = 'Dados insuficientes';
    header('Location: ../treinamentos/listar.php');
    exit;
}

// Instancia controller
$controller = new ParticipanteController();

// Busca dados do participante
$participante = $controller->buscarPorId($participanteId);

if (!$participante) {
    $_SESSION['error_message'] = 'Participante não encontrado';
    header('Location: ../treinamentos/listar.php');
    exit;
}

// Processa formulário
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $controller->processarAvaliacao($participanteId);

    if ($resultado['success']) {
        $_SESSION['success_message'] = $resultado['message'];
        header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
        exit;
    } else {
        $erro = $resultado['message'];
    }
}

// Configurações da página
$pageTitle = 'Avaliar Participante';
$breadcrumb = '<a href="../dashboard.php">Dashboard</a> > <a href="../treinamentos/listar.php">Treinamentos</a> > <a href="../treinamentos/visualizar.php?id=' . $treinamentoId . '">' . e($participante['treinamento_nome']) . '</a> > <a href="gerenciar.php?treinamento_id=' . $treinamentoId . '">Participantes</a> > Avaliar';

// Inclui header
include __DIR__ . '/../../app/views/layouts/header.php';
?>

<style>
    .form-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 800px;
        margin: 0 auto;
    }

    .header-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .header-info h2 {
        margin: 0 0 15px 0;
        font-size: 24px;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        font-size: 14px;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section h3 {
        color: #333;
        font-size: 18px;
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }

    .form-section .description {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
        color: #666;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group label .required {
        color: #dc3545;
    }

    .form-group input[type="number"],
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e1e8ed;
        border-radius: 5px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-group input[type="number"]:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
    }

    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-group .hint {
        display: block;
        font-size: 12px;
        color: #999;
        margin-top: 5px;
    }

    .rating-input {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .rating-input input {
        width: 100px !important;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
    }

    .rating-display {
        font-size: 24px;
        color: #667eea;
        min-width: 50px;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: #667eea;
        color: white;
        flex: 1;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }
</style>

<!-- Header com informações -->
<div class="header-info">
    <h2>⭐ Avaliar Participante</h2>
    <div class="info-row">
        <div>👤 <strong><?php echo e($participante['colaborador_nome']); ?></strong></div>
        <div>📧 <?php echo e($participante['colaborador_email']); ?></div>
        <div>📚 <?php echo e($participante['treinamento_nome']); ?></div>
        <div>📊 Status: <?php echo e($participante['status_participacao']); ?></div>
    </div>
</div>

<?php if ($erro): ?>
    <div class="alert alert-error">
        ❌ <?php echo $erro; ?>
    </div>
<?php endif; ?>

<!-- Formulário -->
<div class="form-card">
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <!-- Avaliação de Reação -->
        <div class="form-section">
            <h3>📊 Nível 1: Avaliação de Reação</h3>
            <div class="description">
                Mede a satisfação imediata do participante com o treinamento (conteúdo, instrutor, material, etc.)
            </div>
            <div class="form-group">
                <label>Nota (0 a 10)</label>
                <div class="rating-input">
                    <input type="number" name="nota_reacao" id="nota_reacao"
                           min="0" max="10" step="0.5"
                           value="<?php echo e($participante['nota_avaliacao_reacao'] ?? ''); ?>"
                           onchange="updateRatingDisplay('reacao')">
                    <div class="rating-display" id="display_reacao">-</div>
                </div>
                <span class="hint">Avalie de 0 (muito insatisfeito) a 10 (muito satisfeito)</span>
            </div>
        </div>

        <!-- Avaliação de Aprendizado -->
        <div class="form-section">
            <h3>📚 Nível 2: Avaliação de Aprendizado</h3>
            <div class="description">
                Mede o quanto o participante aprendeu e absorveu o conteúdo (conhecimentos, habilidades)
            </div>
            <div class="form-group">
                <label>Nota (0 a 10)</label>
                <div class="rating-input">
                    <input type="number" name="nota_aprendizado" id="nota_aprendizado"
                           min="0" max="10" step="0.5"
                           value="<?php echo e($participante['nota_avaliacao_aprendizado'] ?? ''); ?>"
                           onchange="updateRatingDisplay('aprendizado')">
                    <div class="rating-display" id="display_aprendizado">-</div>
                </div>
                <span class="hint">Avalie de 0 (não aprendeu) a 10 (dominou completamente)</span>
            </div>
        </div>

        <!-- Avaliação de Comportamento -->
        <div class="form-section">
            <h3>🎯 Nível 3: Avaliação de Comportamento</h3>
            <div class="description">
                Mede se o participante aplicou o aprendizado no trabalho (mudança de comportamento)
            </div>
            <div class="form-group">
                <label>Nota (0 a 10)</label>
                <div class="rating-input">
                    <input type="number" name="nota_comportamento" id="nota_comportamento"
                           min="0" max="10" step="0.5"
                           value="<?php echo e($participante['nota_avaliacao_comportamento'] ?? ''); ?>"
                           onchange="updateRatingDisplay('comportamento')">
                    <div class="rating-display" id="display_comportamento">-</div>
                </div>
                <span class="hint">Avalie de 0 (sem mudança) a 10 (mudança total no comportamento)</span>
            </div>
        </div>

        <!-- Comentários -->
        <div class="form-section">
            <h3>💬 Comentários Adicionais</h3>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="comentario" placeholder="Comentários sobre o desempenho do participante..."><?php echo e($participante['comentario_avaliacao'] ?? ''); ?></textarea>
                <span class="hint">Pontos fortes, áreas de melhoria, recomendações, etc.</span>
            </div>
        </div>

        <!-- Botões -->
        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                💾 Salvar Avaliação
            </button>
            <a href="gerenciar.php?treinamento_id=<?php echo $treinamentoId; ?>" class="btn btn-secondary">
                ← Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Atualiza displays das notas
function updateRatingDisplay(type) {
    const input = document.getElementById('nota_' + type);
    const display = document.getElementById('display_' + type);
    const value = parseFloat(input.value);

    if (isNaN(value) || value < 0 || value > 10) {
        display.textContent = '-';
        return;
    }

    display.textContent = value.toFixed(1);

    // Muda cor baseado na nota
    if (value >= 8) {
        display.style.color = '#28a745'; // Verde
    } else if (value >= 6) {
        display.style.color = '#ffc107'; // Amarelo
    } else if (value >= 4) {
        display.style.color = '#fd7e14'; // Laranja
    } else {
        display.style.color = '#dc3545'; // Vermelho
    }
}

// Inicializa displays ao carregar
document.addEventListener('DOMContentLoaded', function() {
    updateRatingDisplay('reacao');
    updateRatingDisplay('aprendizado');
    updateRatingDisplay('comportamento');
});
</script>

<?php include __DIR__ . '/../../app/views/layouts/footer.php'; ?>
