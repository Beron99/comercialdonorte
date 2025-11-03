<?php
/**
 * View: Listar Colaboradores
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações e classes
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/Colaborador.php';
require_once __DIR__ . '/../../app/controllers/ColaboradorController.php';

// Configurações da página
$pageTitle = 'Colaboradores';
$breadcrumb = '<a href="../dashboard.php">Dashboard</a> > Colaboradores';

// Instancia controller
$controller = new ColaboradorController();

// Processa exportação se solicitada
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $controller->exportarCSV($_GET);
}

// Lista colaboradores
$resultado = $controller->listar();
$colaboradores = $resultado['data'];
$pagination = [
    'total' => $resultado['total'],
    'page' => $resultado['page'],
    'per_page' => $resultado['per_page'],
    'total_pages' => $resultado['total_pages']
];

// Inclui header
include __DIR__ . '/../../app/views/layouts/header.php';
?>

<style>
    .page-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
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
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
        transform: translateY(-2px);
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 13px;
    }

    .search-filters {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .filter-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
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

    .table-container {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
        color: #2c3e50;
        border-bottom: 2px solid #e1e8ed;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #f8f9fa;
    }

    tbody tr:hover {
        background: #f8f9fa;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        padding: 20px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid #e1e8ed;
        border-radius: 5px;
        text-decoration: none;
        color: #667eea;
    }

    .pagination .active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .stats-bar {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .stat-item {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        flex: 1;
        min-width: 150px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #667eea;
    }

    .stat-label {
        font-size: 12px;
        color: #999;
        text-transform: uppercase;
    }
</style>

<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-value"><?php echo number_format($pagination['total'], 0, ',', '.'); ?></div>
        <div class="stat-label">Total de Colaboradores</div>
    </div>
</div>

<div class="page-actions">
    <h2 style="margin: 0;">📋 Lista de Colaboradores</h2>
    <div style="display: flex; gap: 10px;">
        <a href="cadastrar.php" class="btn btn-primary">
            ➕ Novo Colaborador
        </a>
        <a href="?export=csv<?php echo !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" class="btn btn-success">
            📥 Exportar CSV
        </a>
    </div>
</div>

<div class="search-filters">
    <form method="GET" action="">
        <div class="filter-group">
            <input type="text" name="search" placeholder="🔍 Buscar por nome, email ou CPF..."
                   value="<?php echo e($_GET['search'] ?? ''); ?>">

            <select name="nivel">
                <option value="">Todos os Níveis</option>
                <option value="Estratégico" <?php echo ($_GET['nivel'] ?? '') === 'Estratégico' ? 'selected' : ''; ?>>Estratégico</option>
                <option value="Tático" <?php echo ($_GET['nivel'] ?? '') === 'Tático' ? 'selected' : ''; ?>>Tático</option>
                <option value="Operacional" <?php echo ($_GET['nivel'] ?? '') === 'Operacional' ? 'selected' : ''; ?>>Operacional</option>
            </select>

            <select name="ativo">
                <option value="">Todos os Status</option>
                <option value="1" <?php echo ($_GET['ativo'] ?? '') === '1' ? 'selected' : ''; ?>>Ativos</option>
                <option value="0" <?php echo ($_GET['ativo'] ?? '') === '0' ? 'selected' : ''; ?>>Inativos</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
            <a href="listar.php" class="btn btn-secondary">🔄 Limpar Filtros</a>
        </div>
    </form>
</div>

<div class="table-container">
    <?php if (empty($colaboradores)): ?>
        <div class="no-data">
            <p style="font-size: 48px; margin-bottom: 10px;">📭</p>
            <p>Nenhum colaborador encontrado</p>
            <a href="cadastrar.php" class="btn btn-primary" style="margin-top: 20px;">➕ Cadastrar Primeiro Colaborador</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Nível</th>
                    <th>Cargo</th>
                    <th>Status</th>
                    <th>Origem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($colaboradores as $col): ?>
                <tr>
                    <td><?php echo $col['id']; ?></td>
                    <td><strong><?php echo e($col['nome']); ?></strong></td>
                    <td><?php echo e($col['email']); ?></td>
                    <td>
                        <span class="badge badge-info"><?php echo e($col['nivel_hierarquico']); ?></span>
                    </td>
                    <td><?php echo e($col['cargo'] ?? '-'); ?></td>
                    <td>
                        <?php if ($col['ativo']): ?>
                            <span class="badge badge-success">✅ Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-danger">❌ Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($col['origem'] === 'wordpress'): ?>
                            <span class="badge badge-info">WordPress</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Local</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="visualizar.php?id=<?php echo $col['id']; ?>" class="btn btn-sm btn-primary" title="Visualizar">
                                👁️
                            </a>
                            <a href="editar.php?id=<?php echo $col['id']; ?>" class="btn btn-sm btn-secondary" title="Editar">
                                ✏️
                            </a>
                            <?php if ($col['ativo']): ?>
                                <a href="actions.php?action=inativar&id=<?php echo $col['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Deseja realmente inativar este colaborador?')"
                                   title="Inativar">
                                    ❌
                                </a>
                            <?php else: ?>
                                <a href="actions.php?action=ativar&id=<?php echo $col['id']; ?>"
                                   class="btn btn-sm btn-success"
                                   title="Ativar">
                                    ✅
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['page'] > 1): ?>
                <a href="?page=<?php echo $pagination['page'] - 1; ?><?php echo !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">
                    ← Anterior
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i == $pagination['page']): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                <a href="?page=<?php echo $pagination['page'] + 1; ?><?php echo !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">
                    Próxima →
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../app/views/layouts/footer.php'; ?>
