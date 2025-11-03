<?php
/**
 * View: Editar Treinamento
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações e classes
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/Treinamento.php';
require_once __DIR__ . '/../../app/controllers/TreinamentoController.php';

// Verifica se ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = 'Treinamento não encontrado';
    header('Location: listar.php');
    exit;
}

$id = (int) $_GET['id'];

// Configurações da página
$pageTitle = 'Editar Treinamento';
$breadcrumb = '<a href="../dashboard.php">Dashboard</a> > <a href="listar.php">Treinamentos</a> > Editar';

// Instancia controller
$controller = new TreinamentoController();

// Busca dados do treinamento
$treinamento = $controller->exibirFormularioEdicao($id);

if (!$treinamento) {
    $_SESSION['error_message'] = 'Treinamento não encontrado';
    header('Location: listar.php');
    exit;
}

// Processa formulário
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $controller->processarEdicao($id);

    if ($resultado['success']) {
        $_SESSION['success_message'] = $resultado['message'];
        header('Location: listar.php');
        exit;
    } else {
        $erro = $resultado['message'];
    }
}

// Inclui header
include __DIR__ . '/../../app/views/layouts/header.php';
?>

<style>
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 900px;
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

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        border: 2px solid #e1e8ed;
        border-radius: 5px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-group small {
        display: block;
        margin-top: 5px;
        color: #999;
        font-size: 12px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
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

    .section-title {
        color: #667eea;
        font-size: 18px;
        font-weight: 600;
        margin: 30px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-title:first-child {
        margin-top: 0;
    }

    .info-box {
        background: #e7f3ff;
        border-left: 4px solid #0066cc;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .info-box p {
        margin: 0;
        color: #0066cc;
        font-size: 14px;
    }

    #campos-externo {
        display: none;
    }

    .meta-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #666;
    }

    .meta-info strong {
        color: #333;
    }
</style>

<div class="form-container">
    <h2>✏️ Editar Treinamento</h2>
    <p style="color: #666; margin-bottom: 20px;">Atualize os dados do treinamento abaixo</p>

    <div class="meta-info">
        <strong>ID:</strong> <?php echo $treinamento['id']; ?> |
        <strong>Cadastrado em:</strong> <?php echo date('d/m/Y H:i', strtotime($treinamento['created_at'])); ?>
        <?php if ($treinamento['updated_at'] && $treinamento['updated_at'] != $treinamento['created_at']): ?>
            | <strong>Última atualização:</strong> <?php echo date('d/m/Y H:i', strtotime($treinamento['updated_at'])); ?>
        <?php endif; ?>
    </div>

    <?php if ($erro): ?>
        <div class="alert alert-error">
            ❌ <?php echo $erro; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <div class="section-title">📋 Dados Básicos</div>

        <div class="form-group">
            <label>Nome do Treinamento <span class="required">*</span></label>
            <input type="text" name="nome" required
                   value="<?php echo e($_POST['nome'] ?? $treinamento['nome']); ?>"
                   placeholder="Ex: NR-35 - Trabalho em Altura">
            <small>Informe o nome completo do treinamento ou capacitação</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tipo <span class="required">*</span></label>
                <select name="tipo" id="tipo" required onchange="toggleCamposExterno()">
                    <option value="">Selecione...</option>
                    <option value="Interno" <?php echo ($_POST['tipo'] ?? $treinamento['tipo']) === 'Interno' ? 'selected' : ''; ?>>
                        Interno (Realizado pela empresa)
                    </option>
                    <option value="Externo" <?php echo ($_POST['tipo'] ?? $treinamento['tipo']) === 'Externo' ? 'selected' : ''; ?>>
                        Externo (Fornecedor externo)
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <select name="status" required>
                    <option value="Programado" <?php echo ($_POST['status'] ?? $treinamento['status']) === 'Programado' ? 'selected' : ''; ?>>
                        Programado
                    </option>
                    <option value="Em Andamento" <?php echo ($_POST['status'] ?? $treinamento['status']) === 'Em Andamento' ? 'selected' : ''; ?>>
                        Em Andamento
                    </option>
                    <option value="Executado" <?php echo ($_POST['status'] ?? $treinamento['status']) === 'Executado' ? 'selected' : ''; ?>>
                        Executado
                    </option>
                    <option value="Cancelado" <?php echo ($_POST['status'] ?? $treinamento['status']) === 'Cancelado' ? 'selected' : ''; ?>>
                        Cancelado
                    </option>
                </select>
            </div>
        </div>

        <div id="campos-externo">
            <div class="info-box">
                <p>ℹ️ Campos para treinamentos externos (fornecidos por empresas parceiras)</p>
            </div>

            <div class="form-group">
                <label>Fornecedor/Instituição</label>
                <input type="text" name="fornecedor"
                       value="<?php echo e($_POST['fornecedor'] ?? $treinamento['fornecedor']); ?>"
                       placeholder="Ex: SENAI, SESI, Nome da empresa">
                <small>Nome da empresa ou instituição que fornecerá o treinamento</small>
            </div>
        </div>

        <div class="form-group">
            <label>Instrutor/Responsável</label>
            <input type="text" name="instrutor"
                   value="<?php echo e($_POST['instrutor'] ?? $treinamento['instrutor']); ?>"
                   placeholder="Nome do instrutor ou responsável pelo treinamento">
        </div>

        <div class="section-title">📅 Período e Carga Horária</div>

        <div class="form-row">
            <div class="form-group">
                <label>Data de Início</label>
                <input type="date" name="data_inicio"
                       value="<?php echo e($_POST['data_inicio'] ?? $treinamento['data_inicio']); ?>">
            </div>

            <div class="form-group">
                <label>Data de Término</label>
                <input type="date" name="data_fim"
                       value="<?php echo e($_POST['data_fim'] ?? $treinamento['data_fim']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Carga Horária (horas)</label>
                <input type="number" name="carga_horaria" step="0.5" min="0"
                       value="<?php echo e($_POST['carga_horaria'] ?? $treinamento['carga_horaria']); ?>"
                       placeholder="Ex: 8">
                <small>Carga horária total do treinamento</small>
            </div>

            <div class="form-group">
                <label>Carga Horária Complementar (horas)</label>
                <input type="number" name="carga_horaria_complementar" step="0.5" min="0"
                       value="<?php echo e($_POST['carga_horaria_complementar'] ?? $treinamento['carga_horaria_complementar']); ?>"
                       placeholder="Ex: 2">
                <small>Horas de estudos complementares, se houver</small>
            </div>
        </div>

        <div class="section-title">💰 Informações Financeiras</div>

        <div class="form-group">
            <label>Custo Total (R$)</label>
            <input type="text" name="custo_total" id="custo_total"
                   value="<?php
                       $custo = $_POST['custo_total'] ?? $treinamento['custo_total'];
                       if ($custo && !isset($_POST['custo_total'])) {
                           echo number_format($custo, 2, ',', '.');
                       } else {
                           echo e($custo);
                       }
                   ?>"
                   placeholder="0,00"
                   onkeyup="formatarMoeda(this)">
            <small>Custo total do treinamento (incluindo taxas, materiais, etc.)</small>
        </div>

        <div class="section-title">📝 Informações Adicionais</div>

        <div class="form-group">
            <label>Observações</label>
            <textarea name="observacoes" placeholder="Informações adicionais sobre o treinamento, objetivos, requisitos, etc."><?php echo e($_POST['observacoes'] ?? $treinamento['observacoes']); ?></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                ✅ Salvar Alterações
            </button>
            <a href="listar.php" class="btn btn-secondary">
                ❌ Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Mostra/oculta campos de treinamento externo
function toggleCamposExterno() {
    const tipo = document.getElementById('tipo').value;
    const camposExterno = document.getElementById('campos-externo');

    if (tipo === 'Externo') {
        camposExterno.style.display = 'block';
    } else {
        camposExterno.style.display = 'none';
    }
}

// Chama ao carregar a página para manter estado
document.addEventListener('DOMContentLoaded', function() {
    toggleCamposExterno();
});

// Formatação de Moeda
function formatarMoeda(campo) {
    let valor = campo.value.replace(/\D/g, '');
    if (valor === '') {
        campo.value = '';
        return;
    }
    valor = (parseInt(valor) / 100).toFixed(2);
    valor = valor.replace('.', ',');
    valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    campo.value = valor;
}
</script>

<?php include __DIR__ . '/../../app/views/layouts/footer.php'; ?>
