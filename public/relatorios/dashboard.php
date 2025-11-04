<?php
/**
 * View: Dashboard de Relatórios
 * Visão geral com estatísticas e gráficos
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações e classes
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/Relatorio.php';
require_once __DIR__ . '/../../app/controllers/RelatorioController.php';

// Verifica autenticação
Auth::requireLogin(BASE_URL);

// Instancia controller
$controller = new RelatorioController();

// Busca dados
$stats = $controller->getDashboard();
$relatorioGeral = $controller->getRelatorioGeral();

// Configurações da página
$pageTitle = 'Relatórios e Estatísticas';
$breadcrumb = '<a href="../dashboard.php">Dashboard</a> > Relatórios';

// Inclui header
include __DIR__ . '/../../app/views/layouts/header.php';
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .stat-card .icon {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .stat-card .label {
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .stat-card .value {
        font-size: 32px;
        font-weight: bold;
        color: #667eea;
    }

    .stat-card.success .value {
        color: #28a745;
    }

    .stat-card.warning .value {
        color: #ffc107;
    }

    .stat-card.danger .value {
        color: #dc3545;
    }

    .section-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .section-card h3 {
        color: #333;
        font-size: 20px;
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid #667eea;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 2px solid #e1e8ed;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    tr:hover {
        background: #f8f9ff;
    }

    .progress-bar {
        background: #e1e8ed;
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 5px;
    }

    .progress-fill {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 100%;
        transition: width 0.3s;
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

    .actions-bar {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 20px;
    }

    .chart-placeholder {
        background: #f8f9fa;
        height: 300px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 14px;
    }
</style>

<!-- Ações -->
<div class="actions-bar">
    <a href="geral.php" class="btn btn-primary">
        📊 Relatório Geral
    </a>
    <a href="departamentos.php" class="btn btn-primary">
        🏢 Por Departamento
    </a>
    <a href="matriz.php" class="btn btn-primary">
        📋 Matriz de Capacitações
    </a>
</div>

<!-- Cards de Estatísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="icon">👥</div>
        <div class="label">Colaboradores Ativos</div>
        <div class="value"><?php echo number_format($stats['total_colaboradores'], 0, ',', '.'); ?></div>
    </div>

    <div class="stat-card">
        <div class="icon">📚</div>
        <div class="label">Total de Treinamentos</div>
        <div class="value"><?php echo number_format($stats['total_treinamentos'], 0, ',', '.'); ?></div>
    </div>

    <div class="stat-card success">
        <div class="icon">✅</div>
        <div class="label">Treinamentos Executados</div>
        <div class="value"><?php echo number_format($stats['treinamentos_executado'] ?? 0, 0, ',', '.'); ?></div>
    </div>

    <div class="stat-card warning">
        <div class="icon">⏳</div>
        <div class="label">Em Andamento</div>
        <div class="value"><?php echo number_format($stats['treinamentos_em_andamento'] ?? 0, 0, ',', '.'); ?></div>
    </div>

    <div class="stat-card">
        <div class="icon">🎯</div>
        <div class="label">Total de Participações</div>
        <div class="value"><?php echo number_format($stats['total_participacoes'], 0, ',', '.'); ?></div>
    </div>

    <div class="stat-card">
        <div class="icon">📝</div>
        <div class="label">Check-ins Realizados</div>
        <div class="value"><?php echo number_format($stats['total_checkins'], 0, ',', '.'); ?></div>
    </div>

    <div class="stat-card">
        <div class="icon">⏱️</div>
        <div class="label">Horas de Treinamento</div>
        <div class="value"><?php echo number_format($stats['total_horas_executadas'], 0, ',', '.'); ?>h</div>
    </div>

    <div class="stat-card">
        <div class="icon">💰</div>
        <div class="label">Investimento Total</div>
        <div class="value">R$ <?php echo number_format($stats['total_investimento'], 2, ',', '.'); ?></div>
    </div>

    <div class="stat-card success">
        <div class="icon">⭐</div>
        <div class="label">Avaliação Média Geral</div>
        <div class="value"><?php echo number_format($stats['media_avaliacao_geral'], 1, ',', '.'); ?></div>
    </div>
</div>

<!-- Treinamentos Mais Realizados -->
<div class="section-card">
    <h3>📊 Treinamentos Mais Realizados</h3>
    <?php if (empty($relatorioGeral['treinamentos_mais_realizados'])): ?>
        <p style="text-align: center; color: #999; padding: 40px 0;">Nenhum treinamento executado ainda</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th style="text-align: center;">Participantes</th>
                    <th style="text-align: center;">Avaliação</th>
                    <th style="width: 30%;">Desempenho</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($relatorioGeral['treinamentos_mais_realizados'] as $t): ?>
                    <tr>
                        <td><strong><?php echo e($t['nome']); ?></strong></td>
                        <td><?php echo e($t['tipo']); ?></td>
                        <td style="text-align: center;"><?php echo $t['total_participantes']; ?></td>
                        <td style="text-align: center;">
                            <strong><?php echo number_format($t['media_avaliacao'] ?? 0, 1); ?></strong> / 10
                        </td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo ($t['media_avaliacao'] ?? 0) * 10; ?>%"></div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Colaboradores Mais Capacitados -->
<div class="section-card">
    <h3>🏆 Colaboradores Mais Capacitados</h3>
    <?php if (empty($relatorioGeral['colaboradores_mais_capacitados'])): ?>
        <p style="text-align: center; color: #999; padding: 40px 0;">Nenhum participante registrado ainda</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Colaborador</th>
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th style="text-align: center;">Treinamentos</th>
                    <th style="text-align: center;">Horas</th>
                    <th style="text-align: center;">Avaliação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($relatorioGeral['colaboradores_mais_capacitados'] as $c): ?>
                    <tr>
                        <td><strong><?php echo e($c['nome']); ?></strong></td>
                        <td><?php echo e($c['cargo'] ?? '-'); ?></td>
                        <td><?php echo e($c['departamento'] ?? '-'); ?></td>
                        <td style="text-align: center;"><?php echo $c['total_treinamentos']; ?></td>
                        <td style="text-align: center;"><?php echo number_format($c['total_horas'] ?? 0, 0); ?>h</td>
                        <td style="text-align: center;">
                            <strong><?php echo number_format($c['media_avaliacao'] ?? 0, 1); ?></strong> / 10
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Distribuição por Tipo -->
<div class="section-card">
    <h3>📂 Distribuição por Tipo de Treinamento</h3>
    <?php if (empty($relatorioGeral['distribuicao_tipo'])): ?>
        <p style="text-align: center; color: #999; padding: 40px 0;">Nenhum treinamento cadastrado ainda</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th style="text-align: center;">Total</th>
                    <th style="text-align: right;">Custo Total</th>
                    <th style="width: 40%;">Distribuição</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalGeral = array_sum(array_column($relatorioGeral['distribuicao_tipo'], 'total'));
                foreach ($relatorioGeral['distribuicao_tipo'] as $d):
                    $percentual = $totalGeral > 0 ? ($d['total'] / $totalGeral) * 100 : 0;
                ?>
                    <tr>
                        <td><strong><?php echo e($d['tipo']); ?></strong></td>
                        <td style="text-align: center;"><?php echo $d['total']; ?></td>
                        <td style="text-align: right;">R$ <?php echo number_format($d['custo_total'] ?? 0, 2, ',', '.'); ?></td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $percentual; ?>%"></div>
                            </div>
                            <small><?php echo number_format($percentual, 1); ?>%</small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../app/views/layouts/footer.php'; ?>
