<?php
/**
 * View: Vincular Participantes ao Treinamento
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações e classes
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/Treinamento.php';
require_once __DIR__ . '/../../app/models/Participante.php';
require_once __DIR__ . '/../../app/models/Colaborador.php';
require_once __DIR__ . '/../../app/controllers/ParticipanteController.php';
require_once __DIR__ . '/../../app/controllers/TreinamentoController.php';

// Verifica se treinamento foi informado
$treinamentoId = $_GET['treinamento_id'] ?? 0;

if (!$treinamentoId) {
    $_SESSION['error_message'] = 'Treinamento não informado';
    header('Location: ../treinamentos/listar.php');
    exit;
}

// Instancia controllers
$participanteController = new ParticipanteController();
$treinamentoController = new TreinamentoController();

// Busca dados do treinamento
$treinamentoModel = new Treinamento();
$treinamento = $treinamentoModel->buscarPorId($treinamentoId);

if (!$treinamento) {
    $_SESSION['error_message'] = 'Treinamento não encontrado';
    header('Location: ../treinamentos/listar.php');
    exit;
}

// Processa formulário
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $participanteController->processarVinculacao();

    if ($resultado['success']) {
        $_SESSION['success_message'] = $resultado['message'];
        header('Location: gerenciar.php?treinamento_id=' . $treinamentoId);
        exit;
    } else {
        $erro = $resultado['message'];
    }
}

// Busca colaboradores disponíveis
$filtros = [
    'search' => $_GET['search'] ?? '',
    'nivel' => $_GET['nivel'] ?? '',
    'departamento' => $_GET['departamento'] ?? ''
];

$colaboradoresDisponiveis = $participanteController->buscarColaboradoresDisponiveis($treinamentoId, $filtros);

// Configurações da página
$pageTitle = 'Vincular Participantes';
$breadcrumb = '<a href="../dashboard.php">Dashboard</a> > <a href="../treinamentos/listar.php">Treinamentos</a> > <a href="../treinamentos/visualizar.php?id=' . $treinamentoId . '">' . e($treinamento['nome']) . '</a> > Vincular Participantes';

// Inclui header
include __DIR__ . '/../../app/views/layouts/header.php';
?>

<style>
    .header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .header-card h2 {
        margin: 0 0 10px 0;
        font-size: 24px;
    }

    .filters-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .filters-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 15px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
    }

    .filter-group input,
    .filter-group select {
        padding: 10px;
        border: 2px solid #e1e8ed;
        border-radius: 5px;
        font-size: 14px;
    }

    .colaboradores-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .colaborador-card {
        background: white;
        border: 2px solid #e1e8ed;
        border-radius: 10px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }

    .colaborador-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }

    .colaborador-card.selected {
        border-color: #667eea;
        background: #f8f9ff;
    }

    .colaborador-card input[type="checkbox"] {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .colaborador-info h4 {
        margin: 0 30px 10px 0;
        color: #333;
        font-size: 16px;
    }

    .colaborador-info p {
        margin: 5px 0;
        color: #666;
        font-size: 13px;
    }

    .colaborador-info .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 10px;
    }

    .badge-estrategico {
        background: #e7f3ff;
        color: #0066cc;
    }

    .badge-tatico {
        background: #fff3cd;
        color: #856404;
    }

    .badge-operacional {
        background: #d4edda;
        color: #155724;
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
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-primary:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .selection-bar {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 20px;
        z-index: 100;
    }

    .selection-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .selection-count {
        background: #667eea;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 10px;
        color: #999;
    }

    .empty-state .icon {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .select-all-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .select-all-checkbox input {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .select-all-checkbox label {
        font-weight: 600;
        cursor: pointer;
        margin: 0;
    }
</style>

<!-- Header -->
<div class="header-card">
    <h2>👥 Vincular Participantes</h2>
    <p><?php echo e($treinamento['nome']); ?></p>
</div>

<?php if ($erro): ?>
    <div class="alert alert-error">
        ❌ <?php echo $erro; ?>
    </div>
<?php endif; ?>

<!-- Filtros -->
<div class="filters-card">
    <form method="GET" action="">
        <input type="hidden" name="treinamento_id" value="<?php echo $treinamentoId; ?>">
        <div class="filters-row">
            <div class="filter-group">
                <label>🔍 Buscar</label>
                <input type="text" name="search" placeholder="Nome, email ou cargo..."
                       value="<?php echo e($_GET['search'] ?? ''); ?>">
            </div>

            <div class="filter-group">
                <label>📊 Nível</label>
                <select name="nivel">
                    <option value="">Todos</option>
                    <option value="Estratégico" <?php echo ($_GET['nivel'] ?? '') === 'Estratégico' ? 'selected' : ''; ?>>Estratégico</option>
                    <option value="Tático" <?php echo ($_GET['nivel'] ?? '') === 'Tático' ? 'selected' : ''; ?>>Tático</option>
                    <option value="Operacional" <?php echo ($_GET['nivel'] ?? '') === 'Operacional' ? 'selected' : ''; ?>>Operacional</option>
                </select>
            </div>

            <div class="filter-group">
                <label>🏢 Departamento</label>
                <input type="text" name="departamento" placeholder="Departamento..."
                       value="<?php echo e($_GET['departamento'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>
</div>

<!-- Formulário de Vinculação -->
<form method="POST" action="" id="formVincular">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="treinamento_id" value="<?php echo $treinamentoId; ?>">

    <!-- Barra de Seleção -->
    <div class="selection-bar">
        <div class="selection-info">
            <span class="selection-count">
                <span id="countSelected">0</span> selecionado(s)
            </span>
            <span>de <?php echo count($colaboradoresDisponiveis); ?> disponíveis</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" id="btnVincular" disabled>
                ➕ Vincular Selecionados
            </button>
            <a href="gerenciar.php?treinamento_id=<?php echo $treinamentoId; ?>" class="btn btn-secondary">
                ← Voltar
            </a>
        </div>
    </div>

    <?php if (empty($colaboradoresDisponiveis)): ?>
        <div class="empty-state">
            <div class="icon">👥</div>
            <h3>Nenhum colaborador disponível</h3>
            <p>Todos os colaboradores ativos já estão vinculados a este treinamento</p>
            <a href="gerenciar.php?treinamento_id=<?php echo $treinamentoId; ?>" class="btn btn-secondary" style="margin-top: 20px;">
                ← Voltar para Gerenciamento
            </a>
        </div>
    <?php else: ?>
        <!-- Selecionar Todos -->
        <div class="select-all-checkbox">
            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
            <label for="selectAll">Selecionar todos os colaboradores desta página</label>
        </div>

        <!-- Grid de Colaboradores -->
        <div class="colaboradores-grid">
            <?php foreach ($colaboradoresDisponiveis as $colaborador): ?>
                <div class="colaborador-card" onclick="toggleCard(<?php echo $colaborador['id']; ?>)">
                    <input type="checkbox" name="colaboradores[]" value="<?php echo $colaborador['id']; ?>"
                           id="col-<?php echo $colaborador['id']; ?>"
                           onchange="updateCount(); updateCardState(<?php echo $colaborador['id']; ?>)"
                           onclick="event.stopPropagation();">

                    <div class="colaborador-info">
                        <h4><?php echo e($colaborador['nome']); ?></h4>
                        <p>📧 <?php echo e($colaborador['email']); ?></p>
                        <?php if ($colaborador['cargo']): ?>
                            <p>💼 <?php echo e($colaborador['cargo']); ?></p>
                        <?php endif; ?>
                        <?php if ($colaborador['departamento']): ?>
                            <p>🏢 <?php echo e($colaborador['departamento']); ?></p>
                        <?php endif; ?>
                        <span class="badge badge-<?php echo strtolower($colaborador['nivel_hierarquico']); ?>">
                            <?php echo e($colaborador['nivel_hierarquico']); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</form>

<script>
// Atualiza contador de selecionados
function updateCount() {
    const checkboxes = document.querySelectorAll('input[name="colaboradores[]"]:checked');
    const count = checkboxes.length;
    document.getElementById('countSelected').textContent = count;
    document.getElementById('btnVincular').disabled = count === 0;
}

// Atualiza estado visual do card
function updateCardState(id) {
    const checkbox = document.getElementById('col-' + id);
    const card = checkbox.closest('.colaborador-card');

    if (checkbox.checked) {
        card.classList.add('selected');
    } else {
        card.classList.remove('selected');
    }
}

// Toggle card ao clicar
function toggleCard(id) {
    const checkbox = document.getElementById('col-' + id);
    checkbox.checked = !checkbox.checked;
    updateCount();
    updateCardState(id);
}

// Selecionar todos
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('input[name="colaboradores[]"]');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        updateCardState(parseInt(checkbox.value));
    });

    updateCount();
}

// Atualiza contador ao carregar
document.addEventListener('DOMContentLoaded', function() {
    updateCount();
});
</script>

<?php include __DIR__ . '/../../app/views/layouts/footer.php'; ?>
