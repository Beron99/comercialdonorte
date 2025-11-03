<?php
/**
 * View: Listar Treinamentos
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações e classes
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/Treinamento.php';
require_once __DIR__ . '/../../app/controllers/TreinamentoController.php';

// Instancia controller
$controller = new TreinamentoController();

// Busca lista de treinamentos
$resultado = $controller->listar();
$treinamentos = $resultado['data'];
$totalPaginas = $resultado['total_pages'];
$paginaAtual = $resultado['page'];
$total = $resultado['total'];

// Busca anos disponíveis para filtro
$anosDisponiveis = $controller->getAnosDisponiveis();

// Configurações da página
$pageTitle = 'Treinamentos';
$breadcrumb = '<a href="../dashboard.php">Dashboard</a> > Treinamentos';

// Inclui header
include __DIR__ . '/../../app/views/layouts/header.php';
?>

<style>
    .filters-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .filters-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
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

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #667eea;
    }

    .btn {
        padding: 10px 20px;
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

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .actions-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .table-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f8f9fa;
    }

    th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #e1e8ed;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    tr:hover {
        background: #f8f9fa;
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-primary {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-info {
        background: #e7f3ff;
        color: #0066cc;
    }

    .action-links {
        display: flex;
        gap: 10px;
    }

    .action-links a {
        color: #667eea;
        text-decoration: none;
        font-size: 18px;
        transition: transform 0.2s;
    }

    .action-links a:hover {
        transform: scale(1.2);
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        padding: 20px;
        background: white;
        border-radius: 0 0 10px 10px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid #e1e8ed;
        border-radius: 5px;
        text-decoration: none;
        color: #333;
    }

    .pagination a:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .pagination .active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state .icon {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .filters-row {
            grid-template-columns: 1fr;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 10px 8px;
        }
    }
</style>

<!-- Filtros -->
<div class="filters-card">
    <form method="GET" action="">
        <div class="filters-row">
            <div class="filter-group">
                <label>🔍 Buscar</label>
                <input type="text" name="search" placeholder="Nome, fornecedor ou instrutor..."
                       value="<?php echo e($_GET['search'] ?? ''); ?>">
            </div>

            <div class="filter-group">
                <label>📂 Tipo</label>
                <select name="tipo">
                    <option value="">Todos</option>
                    <option value="Interno" <?php echo ($_GET['tipo'] ?? '') === 'Interno' ? 'selected' : ''; ?>>Interno</option>
                    <option value="Externo" <?php echo ($_GET['tipo'] ?? '') === 'Externo' ? 'selected' : ''; ?>>Externo</option>
                </select>
            </div>

            <div class="filter-group">
                <label>📊 Status</label>
                <select name="status">
                    <option value="">Todos</option>
                    <option value="Programado" <?php echo ($_GET['status'] ?? '') === 'Programado' ? 'selected' : ''; ?>>Programado</option>
                    <option value="Em Andamento" <?php echo ($_GET['status'] ?? '') === 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                    <option value="Executado" <?php echo ($_GET['status'] ?? '') === 'Executado' ? 'selected' : ''; ?>>Executado</option>
                    <option value="Cancelado" <?php echo ($_GET['status'] ?? '') === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </div>

            <div class="filter-group">
                <label>📅 Ano</label>
                <select name="ano">
                    <option value="">Todos</option>
                    <?php foreach ($anosDisponiveis as $ano): ?>
                        <option value="<?php echo $ano; ?>" <?php echo ($_GET['ano'] ?? '') == $ano ? 'selected' : ''; ?>>
                            <?php echo $ano; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>
</div>

<!-- Barra de Ações -->
<div class="actions-bar">
    <div>
        <strong><?php echo number_format($total, 0, ',', '.'); ?></strong>
        treinamento<?php echo $total != 1 ? 's' : ''; ?> encontrado<?php echo $total != 1 ? 's' : ''; ?>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="cadastrar.php" class="btn btn-success">
            ➕ Novo Treinamento
        </a>
        <a href="actions.php?action=exportar<?php echo !empty($_GET) ? '&' . http_build_query($_GET) : ''; ?>" class="btn btn-secondary">
            📥 Exportar CSV
        </a>
    </div>
</div>

<!-- Tabela -->
<div class="table-container">
    <?php if (empty($treinamentos)): ?>
        <div class="empty-state">
            <div class="icon">📚</div>
            <h3>Nenhum treinamento encontrado</h3>
            <p>Comece cadastrando o primeiro treinamento do sistema</p>
            <a href="cadastrar.php" class="btn btn-success" style="margin-top: 20px;">
                ➕ Cadastrar Treinamento
            </a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Período</th>
                    <th>Carga Horária</th>
                    <th>Participantes</th>
                    <th>Status</th>
                    <th style="text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($treinamentos as $treinamento): ?>
                    <tr>
                        <td>
                            <strong><?php echo e($treinamento['nome']); ?></strong><br>
                            <?php if ($treinamento['fornecedor']): ?>
                                <small style="color: #999;">
                                    🏢 <?php echo e($treinamento['fornecedor']); ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $tipoClass = $treinamento['tipo'] === 'Interno' ? 'badge-info' : 'badge-warning';
                            ?>
                            <span class="badge <?php echo $tipoClass; ?>">
                                <?php echo e($treinamento['tipo']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($treinamento['data_inicio']): ?>
                                <?php echo date('d/m/Y', strtotime($treinamento['data_inicio'])); ?>
                                <?php if ($treinamento['data_fim']): ?>
                                    - <?php echo date('d/m/Y', strtotime($treinamento['data_fim'])); ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($treinamento['carga_horaria']): ?>
                                <?php echo number_format($treinamento['carga_horaria'], 1); ?>h
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <strong><?php echo $treinamento['total_participantes']; ?></strong>
                        </td>
                        <td>
                            <?php
                            $statusClass = match($treinamento['status']) {
                                'Programado' => 'badge-primary',
                                'Em Andamento' => 'badge-warning',
                                'Executado' => 'badge-success',
                                'Cancelado' => 'badge-danger',
                                default => 'badge-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo e($treinamento['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="visualizar.php?id=<?php echo $treinamento['id']; ?>" title="Visualizar">👁️</a>
                                <a href="editar.php?id=<?php echo $treinamento['id']; ?>" title="Editar">✏️</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
            <div class="pagination">
                <?php if ($paginaAtual > 1): ?>
                    <a href="?page=<?php echo $paginaAtual - 1; ?><?php echo !empty($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : ''; ?>">
                        ← Anterior
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $paginaAtual - 2); $i <= min($totalPaginas, $paginaAtual + 2); $i++): ?>
                    <?php if ($i == $paginaAtual): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?><?php echo !empty($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a href="?page=<?php echo $paginaAtual + 1; ?><?php echo !empty($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['page' => ''])) : ''; ?>">
                        Próxima →
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../app/views/layouts/footer.php'; ?>
