<?php
/**
 * Model: Relatório
 * Gera dados estatísticos e relatórios do sistema
 */

class Relatorio {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getConnection();
    }

    /**
     * Dashboard - Estatísticas gerais
     */
    public function getDashboardStats() {
        $stats = [];

        // Total de colaboradores ativos
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total
            FROM colaboradores
            WHERE ativo = 1
        ");
        $stats['total_colaboradores'] = $stmt->fetch()['total'];

        // Total de treinamentos
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total
            FROM treinamentos
        ");
        $stats['total_treinamentos'] = $stmt->fetch()['total'];

        // Treinamentos por status
        $stmt = $this->pdo->query("
            SELECT
                status,
                COUNT(*) as total
            FROM treinamentos
            GROUP BY status
        ");
        $statusData = $stmt->fetchAll();
        foreach ($statusData as $row) {
            $stats['treinamentos_' . strtolower(str_replace(' ', '_', $row['status']))] = $row['total'];
        }

        // Total de participações
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total
            FROM treinamento_participantes
        ");
        $stats['total_participacoes'] = $stmt->fetch()['total'];

        // Check-ins realizados
        $stmt = $this->pdo->query("
            SELECT COUNT(*) as total
            FROM treinamento_participantes
            WHERE check_in_realizado = 1
        ");
        $stats['total_checkins'] = $stmt->fetch()['total'];

        // Horas de treinamento (total)
        $stmt = $this->pdo->query("
            SELECT
                SUM(t.carga_horaria) as total_horas
            FROM treinamentos t
            WHERE t.status = 'Executado'
        ");
        $stats['total_horas_executadas'] = $stmt->fetch()['total_horas'] ?? 0;

        // Investimento total
        $stmt = $this->pdo->query("
            SELECT
                SUM(custo_total) as total_investimento
            FROM treinamentos
            WHERE status = 'Executado'
        ");
        $stats['total_investimento'] = $stmt->fetch()['total_investimento'] ?? 0;

        // Média de avaliação geral
        $stmt = $this->pdo->query("
            SELECT
                AVG(nota_avaliacao_reacao) as media_reacao,
                AVG(nota_avaliacao_aprendizado) as media_aprendizado,
                AVG(nota_avaliacao_comportamento) as media_comportamento
            FROM treinamento_participantes
            WHERE nota_avaliacao_reacao IS NOT NULL
        ");
        $avaliacoes = $stmt->fetch();
        $stats['media_avaliacao_reacao'] = $avaliacoes['media_reacao'] ?? 0;
        $stats['media_avaliacao_aprendizado'] = $avaliacoes['media_aprendizado'] ?? 0;
        $stats['media_avaliacao_comportamento'] = $avaliacoes['media_comportamento'] ?? 0;

        // Média geral
        $count = 0;
        $sum = 0;
        if ($stats['media_avaliacao_reacao'] > 0) {
            $sum += $stats['media_avaliacao_reacao'];
            $count++;
        }
        if ($stats['media_avaliacao_aprendizado'] > 0) {
            $sum += $stats['media_avaliacao_aprendizado'];
            $count++;
        }
        if ($stats['media_avaliacao_comportamento'] > 0) {
            $sum += $stats['media_avaliacao_comportamento'];
            $count++;
        }
        $stats['media_avaliacao_geral'] = $count > 0 ? $sum / $count : 0;

        return $stats;
    }

    /**
     * Treinamentos mais realizados
     */
    public function getTreinamentosMaisRealizados($limite = 10) {
        $sql = "SELECT
                t.nome,
                t.tipo,
                COUNT(tp.id) as total_participantes,
                AVG(tp.nota_avaliacao_reacao) as media_avaliacao
                FROM treinamentos t
                LEFT JOIN treinamento_participantes tp ON t.id = tp.treinamento_id
                WHERE t.status = 'Executado'
                GROUP BY t.id
                ORDER BY total_participantes DESC
                LIMIT ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }

    /**
     * Colaboradores com mais treinamentos
     */
    public function getColaboradoresMaisCapacitados($limite = 10) {
        $sql = "SELECT
                c.nome,
                c.cargo,
                c.departamento,
                COUNT(tp.id) as total_treinamentos,
                SUM(t.carga_horaria) as total_horas,
                AVG(tp.nota_avaliacao_reacao) as media_avaliacao
                FROM colaboradores c
                JOIN treinamento_participantes tp ON c.id = tp.colaborador_id
                JOIN treinamentos t ON tp.treinamento_id = t.id
                WHERE c.ativo = 1
                GROUP BY c.id
                ORDER BY total_treinamentos DESC
                LIMIT ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }

    /**
     * Distribuição de treinamentos por tipo
     */
    public function getDistribuicaoPorTipo() {
        $sql = "SELECT
                tipo,
                COUNT(*) as total,
                SUM(custo_total) as custo_total
                FROM treinamentos
                GROUP BY tipo
                ORDER BY total DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Treinamentos por mês (último ano)
     */
    public function getTreinamentosPorMes($ano = null) {
        if (!$ano) {
            $ano = date('Y');
        }

        $sql = "SELECT
                MONTH(data_inicio) as mes,
                COUNT(*) as total,
                SUM(custo_total) as custo_total
                FROM treinamentos
                WHERE YEAR(data_inicio) = ?
                GROUP BY MONTH(data_inicio)
                ORDER BY mes";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ano]);
        return $stmt->fetchAll();
    }

    /**
     * Taxa de presença por treinamento
     */
    public function getTaxaPresenca() {
        $sql = "SELECT
                t.nome,
                t.data_inicio,
                COUNT(tp.id) as total_participantes,
                SUM(CASE WHEN tp.status_participacao = 'Presente' THEN 1 ELSE 0 END) as presentes,
                ROUND((SUM(CASE WHEN tp.status_participacao = 'Presente' THEN 1 ELSE 0 END) / COUNT(tp.id)) * 100, 2) as taxa_presenca
                FROM treinamentos t
                LEFT JOIN treinamento_participantes tp ON t.id = tp.treinamento_id
                WHERE t.status = 'Executado'
                GROUP BY t.id
                HAVING total_participantes > 0
                ORDER BY t.data_inicio DESC
                LIMIT 20";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Relatório por departamento
     */
    public function getRelatorioPorDepartamento() {
        $sql = "SELECT
                c.departamento,
                COUNT(DISTINCT c.id) as total_colaboradores,
                COUNT(tp.id) as total_participacoes,
                SUM(t.carga_horaria) as total_horas,
                SUM(t.custo_total) as total_investimento,
                AVG(tp.nota_avaliacao_reacao) as media_avaliacao
                FROM colaboradores c
                LEFT JOIN treinamento_participantes tp ON c.id = tp.colaborador_id
                LEFT JOIN treinamentos t ON tp.treinamento_id = t.id AND t.status = 'Executado'
                WHERE c.ativo = 1 AND c.departamento IS NOT NULL AND c.departamento != ''
                GROUP BY c.departamento
                ORDER BY total_participacoes DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Relatório por nível hierárquico
     */
    public function getRelatorioPorNivel() {
        $sql = "SELECT
                c.nivel_hierarquico,
                COUNT(DISTINCT c.id) as total_colaboradores,
                COUNT(tp.id) as total_participacoes,
                SUM(t.carga_horaria) as total_horas,
                AVG(tp.nota_avaliacao_reacao) as media_avaliacao
                FROM colaboradores c
                LEFT JOIN treinamento_participantes tp ON c.id = tp.colaborador_id
                LEFT JOIN treinamentos t ON tp.treinamento_id = t.id AND t.status = 'Executado'
                WHERE c.ativo = 1
                GROUP BY c.nivel_hierarquico
                ORDER BY
                    CASE c.nivel_hierarquico
                        WHEN 'Estratégico' THEN 1
                        WHEN 'Tático' THEN 2
                        WHEN 'Operacional' THEN 3
                    END";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Próximos treinamentos (próximos 30 dias)
     */
    public function getProximosTreinamentos() {
        $sql = "SELECT
                t.*,
                COUNT(tp.id) as total_participantes
                FROM treinamentos t
                LEFT JOIN treinamento_participantes tp ON t.id = tp.treinamento_id
                WHERE t.status IN ('Programado', 'Em Andamento')
                AND t.data_inicio >= CURDATE()
                AND t.data_inicio <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                GROUP BY t.id
                ORDER BY t.data_inicio ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Matriz de capacitações (colaboradores x treinamentos)
     */
    public function getMatrizCapacitacoes($departamento = null) {
        $where = '1=1';
        $bindings = [];

        if ($departamento) {
            $where .= ' AND c.departamento = ?';
            $bindings[] = $departamento;
        }

        $sql = "SELECT
                c.id as colaborador_id,
                c.nome as colaborador_nome,
                c.cargo,
                c.departamento,
                GROUP_CONCAT(DISTINCT t.nome ORDER BY t.data_inicio SEPARATOR '|') as treinamentos,
                COUNT(DISTINCT tp.id) as total_treinamentos,
                SUM(t.carga_horaria) as total_horas
                FROM colaboradores c
                LEFT JOIN treinamento_participantes tp ON c.id = tp.colaborador_id
                LEFT JOIN treinamentos t ON tp.treinamento_id = t.id AND t.status = 'Executado'
                WHERE {$where} AND c.ativo = 1
                GROUP BY c.id
                ORDER BY c.nome";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * Evolução mensal de treinamentos (gráfico)
     */
    public function getEvolucaoMensal($meses = 12) {
        $sql = "SELECT
                DATE_FORMAT(data_inicio, '%Y-%m') as mes_ano,
                COUNT(*) as total_treinamentos,
                SUM(custo_total) as investimento,
                COUNT(DISTINCT tp.colaborador_id) as participantes_unicos
                FROM treinamentos t
                LEFT JOIN treinamento_participantes tp ON t.id = tp.treinamento_id
                WHERE t.data_inicio >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(data_inicio, '%Y-%m')
                ORDER BY mes_ano";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$meses]);
        return $stmt->fetchAll();
    }
}
