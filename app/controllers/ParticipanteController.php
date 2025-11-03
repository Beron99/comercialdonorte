<?php
/**
 * Controller: Participante
 * Gerencia vinculação de participantes aos treinamentos
 */

class ParticipanteController {
    private $model;

    public function __construct() {
        $this->model = new Participante();
    }

    /**
     * Processa vinculação de participante
     */
    public function processarVinculacao() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Método inválido'];
        }

        // Valida CSRF
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'message' => 'Token de segurança inválido'];
        }

        // Valida dados
        if (empty($_POST['treinamento_id'])) {
            return ['success' => false, 'message' => 'Treinamento não informado'];
        }

        // Vinculação múltipla ou única
        if (!empty($_POST['colaboradores']) && is_array($_POST['colaboradores'])) {
            return $this->model->vincularMultiplos(
                $_POST['treinamento_id'],
                $_POST['colaboradores'],
                $_POST['status_participacao'] ?? 'Confirmado'
            );
        } elseif (!empty($_POST['colaborador_id'])) {
            return $this->model->vincular([
                'treinamento_id' => $_POST['treinamento_id'],
                'colaborador_id' => $_POST['colaborador_id'],
                'status_participacao' => $_POST['status_participacao'] ?? 'Confirmado',
                'observacoes' => $_POST['observacoes'] ?? null
            ]);
        }

        return ['success' => false, 'message' => 'Nenhum colaborador selecionado'];
    }

    /**
     * Processa desvinculação
     */
    public function processarDesvinculacao($id) {
        // Valida permissão
        if (!Auth::hasLevel(['admin', 'gestor'])) {
            return ['success' => false, 'message' => 'Sem permissão'];
        }

        return $this->model->desvincular($id);
    }

    /**
     * Processa atualização de status
     */
    public function processarAtualizacaoStatus($id, $status) {
        // Valida status
        $statusValidos = ['Confirmado', 'Pendente', 'Ausente', 'Presente', 'Cancelado'];
        if (!in_array($status, $statusValidos)) {
            return ['success' => false, 'message' => 'Status inválido'];
        }

        return $this->model->atualizarStatus($id, $status);
    }

    /**
     * Processa check-in
     */
    public function processarCheckIn($id) {
        return $this->model->registrarCheckIn($id);
    }

    /**
     * Processa avaliação
     */
    public function processarAvaliacao($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Método inválido'];
        }

        // Valida CSRF
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'message' => 'Token de segurança inválido'];
        }

        // Valida notas
        $erros = [];

        if (!empty($_POST['nota_reacao'])) {
            $nota = floatval($_POST['nota_reacao']);
            if ($nota < 0 || $nota > 10) {
                $erros[] = 'Nota de reação deve estar entre 0 e 10';
            }
        }

        if (!empty($_POST['nota_aprendizado'])) {
            $nota = floatval($_POST['nota_aprendizado']);
            if ($nota < 0 || $nota > 10) {
                $erros[] = 'Nota de aprendizado deve estar entre 0 e 10';
            }
        }

        if (!empty($_POST['nota_comportamento'])) {
            $nota = floatval($_POST['nota_comportamento']);
            if ($nota < 0 || $nota > 10) {
                $erros[] = 'Nota de comportamento deve estar entre 0 e 10';
            }
        }

        if (!empty($erros)) {
            return ['success' => false, 'message' => implode('<br>', $erros)];
        }

        $dados = [
            'nota_reacao' => !empty($_POST['nota_reacao']) ? floatval($_POST['nota_reacao']) : null,
            'nota_aprendizado' => !empty($_POST['nota_aprendizado']) ? floatval($_POST['nota_aprendizado']) : null,
            'nota_comportamento' => !empty($_POST['nota_comportamento']) ? floatval($_POST['nota_comportamento']) : null,
            'comentario' => trim($_POST['comentario'] ?? '')
        ];

        return $this->model->registrarAvaliacao($id, $dados);
    }

    /**
     * Lista participantes de um treinamento
     */
    public function listarPorTreinamento($treinamentoId) {
        return $this->model->listarPorTreinamento($treinamentoId);
    }

    /**
     * Busca colaboradores disponíveis
     */
    public function buscarColaboradoresDisponiveis($treinamentoId, $filtros = []) {
        return $this->model->buscarColaboradoresDisponiveis($treinamentoId, $filtros);
    }

    /**
     * Busca participante por ID
     */
    public function buscarPorId($id) {
        return $this->model->buscarPorId($id);
    }

    /**
     * Histórico de treinamentos do colaborador
     */
    public function historicoPorColaborador($colaboradorId) {
        return $this->model->historicoPorColaborador($colaboradorId);
    }

    /**
     * Estatísticas de participação
     */
    public function getEstatisticasTreinamento($treinamentoId) {
        return $this->model->getEstatisticasTreinamento($treinamentoId);
    }

    /**
     * Atualiza observações
     */
    public function atualizarObservacoes($id, $observacoes) {
        return $this->model->atualizarObservacoes($id, $observacoes);
    }

    /**
     * Marca certificado como emitido
     */
    public function marcarCertificadoEmitido($id) {
        return $this->model->marcarCertificadoEmitido($id);
    }

    /**
     * Exporta lista de participantes para CSV
     */
    public function exportarCSV($treinamentoId) {
        $participantes = $this->model->listarPorTreinamento($treinamentoId);

        // Busca nome do treinamento
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("SELECT nome FROM treinamentos WHERE id = ?");
        $stmt->execute([$treinamentoId]);
        $treinamento = $stmt->fetch();

        $nomeArquivo = 'participantes_' . ($treinamento['nome'] ?? 'treinamento') . '_' . date('Y-m-d');
        $nomeArquivo = preg_replace('/[^a-z0-9_-]/i', '_', $nomeArquivo);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $nomeArquivo . '.csv');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Cabeçalho
        fputcsv($output, [
            'ID', 'Nome', 'Email', 'Cargo', 'Departamento', 'Nível',
            'Status', 'Check-in', 'Data Check-in', 'Avaliação Reação',
            'Avaliação Aprendizado', 'Avaliação Comportamento', 'Certificado'
        ], ';');

        // Dados
        foreach ($participantes as $p) {
            fputcsv($output, [
                $p['id'],
                $p['colaborador_nome'],
                $p['colaborador_email'],
                $p['cargo'] ?? '-',
                $p['departamento'] ?? '-',
                $p['nivel_hierarquico'],
                $p['status_participacao'],
                $p['check_in_realizado'] ? 'Sim' : 'Não',
                $p['data_check_in'] ? date('d/m/Y H:i', strtotime($p['data_check_in'])) : '-',
                $p['nota_avaliacao_reacao'] ?? '-',
                $p['nota_avaliacao_aprendizado'] ?? '-',
                $p['nota_avaliacao_comportamento'] ?? '-',
                $p['certificado_emitido'] ? 'Sim' : 'Não'
            ], ';');
        }

        fclose($output);
        exit;
    }
}
