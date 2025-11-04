<?php
/**
 * Controller: Relatório
 * Gerencia geração de relatórios e estatísticas
 */

class RelatorioController {
    private $model;

    public function __construct() {
        $this->model = new Relatorio();
    }

    /**
     * Dados para o dashboard principal
     */
    public function getDashboard() {
        return $this->model->getDashboardStats();
    }

    /**
     * Relatório geral do sistema
     */
    public function getRelatorioGeral() {
        return [
            'stats' => $this->model->getDashboardStats(),
            'treinamentos_mais_realizados' => $this->model->getTreinamentosMaisRealizados(10),
            'colaboradores_mais_capacitados' => $this->model->getColaboradoresMaisCapacitados(10),
            'distribuicao_tipo' => $this->model->getDistribuicaoPorTipo(),
            'taxa_presenca' => $this->model->getTaxaPresenca()
        ];
    }

    /**
     * Relatório por departamento
     */
    public function getRelatorioDepartamentos() {
        return $this->model->getRelatorioPorDepartamento();
    }

    /**
     * Relatório por nível hierárquico
     */
    public function getRelatorioNiveis() {
        return $this->model->getRelatorioPorNivel();
    }

    /**
     * Matriz de capacitações
     */
    public function getMatrizCapacitacoes($departamento = null) {
        return $this->model->getMatrizCapacitacoes($departamento);
    }

    /**
     * Dados para gráficos
     */
    public function getDadosGraficos() {
        $ano = $_GET['ano'] ?? date('Y');

        return [
            'mensal' => $this->model->getTreinamentosPorMes($ano),
            'evolucao' => $this->model->getEvolucaoMensal(12),
            'distribuicao_tipo' => $this->model->getDistribuicaoPorTipo(),
            'por_departamento' => $this->model->getRelatorioPorDepartamento(),
            'por_nivel' => $this->model->getRelatorioNiveis()
        ];
    }

    /**
     * Exporta relatório para CSV
     */
    public function exportarCSV($tipo) {
        $dados = [];
        $colunas = [];
        $nomeArquivo = 'relatorio_' . $tipo . '_' . date('Y-m-d');

        switch ($tipo) {
            case 'geral':
                $relatorio = $this->getRelatorioGeral();
                $dados = $relatorio['treinamentos_mais_realizados'];
                $colunas = ['Nome', 'Tipo', 'Total Participantes', 'Média Avaliação'];
                break;

            case 'departamentos':
                $dados = $this->getRelatorioDepartamentos();
                $colunas = ['Departamento', 'Total Colaboradores', 'Total Participações', 'Total Horas', 'Investimento', 'Média Avaliação'];
                break;

            case 'niveis':
                $dados = $this->getRelatorioNiveis();
                $colunas = ['Nível', 'Total Colaboradores', 'Total Participações', 'Total Horas', 'Média Avaliação'];
                break;

            case 'matriz':
                $departamento = $_GET['departamento'] ?? null;
                $dados = $this->getMatrizCapacitacoes($departamento);
                $colunas = ['Colaborador', 'Cargo', 'Departamento', 'Total Treinamentos', 'Total Horas'];
                break;

            default:
                return ['success' => false, 'message' => 'Tipo de relatório inválido'];
        }

        // Headers para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $nomeArquivo . '.csv');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Cabeçalho
        fputcsv($output, $colunas, ';');

        // Dados
        foreach ($dados as $row) {
            $linha = [];

            switch ($tipo) {
                case 'geral':
                    $linha = [
                        $row['nome'],
                        $row['tipo'],
                        $row['total_participantes'],
                        number_format($row['media_avaliacao'] ?? 0, 2)
                    ];
                    break;

                case 'departamentos':
                    $linha = [
                        $row['departamento'],
                        $row['total_colaboradores'],
                        $row['total_participacoes'],
                        number_format($row['total_horas'] ?? 0, 2),
                        number_format($row['total_investimento'] ?? 0, 2),
                        number_format($row['media_avaliacao'] ?? 0, 2)
                    ];
                    break;

                case 'niveis':
                    $linha = [
                        $row['nivel_hierarquico'],
                        $row['total_colaboradores'],
                        $row['total_participacoes'],
                        number_format($row['total_horas'] ?? 0, 2),
                        number_format($row['media_avaliacao'] ?? 0, 2)
                    ];
                    break;

                case 'matriz':
                    $linha = [
                        $row['colaborador_nome'],
                        $row['cargo'] ?? '-',
                        $row['departamento'] ?? '-',
                        $row['total_treinamentos'],
                        number_format($row['total_horas'] ?? 0, 2)
                    ];
                    break;
            }

            fputcsv($output, $linha, ';');
        }

        fclose($output);
        exit;
    }
}
