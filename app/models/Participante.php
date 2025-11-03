<?php
/**
 * Model: Participante
 * Gerencia vinculação de colaboradores aos treinamentos
 */

class Participante {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getConnection();
    }

    /**
     * Vincula um colaborador a um treinamento
     */
    public function vincular($dados) {
        try {
            // Verifica se já está vinculado
            $stmt = $this->pdo->prepare("
                SELECT id FROM treinamento_participantes
                WHERE treinamento_id = ? AND colaborador_id = ?
            ");
            $stmt->execute([$dados['treinamento_id'], $dados['colaborador_id']]);

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Colaborador já está vinculado a este treinamento'];
            }

            // Insere vinculação
            $sql = "INSERT INTO treinamento_participantes
                    (treinamento_id, colaborador_id, status_participacao, observacoes)
                    VALUES (?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $dados['treinamento_id'],
                $dados['colaborador_id'],
                $dados['status_participacao'] ?? 'Confirmado',
                $dados['observacoes'] ?? null
            ]);

            return [
                'success' => true,
                'message' => 'Participante vinculado com sucesso',
                'id' => $this->pdo->lastInsertId()
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao vincular: ' . $e->getMessage()];
        }
    }

    /**
     * Vincula múltiplos colaboradores de uma vez
     */
    public function vincularMultiplos($treinamentoId, $colaboradoresIds, $statusParticipacao = 'Confirmado') {
        try {
            $this->pdo->beginTransaction();

            $vinculados = 0;
            $jaVinculados = 0;
            $erros = [];

            foreach ($colaboradoresIds as $colaboradorId) {
                // Verifica se já está vinculado
                $stmt = $this->pdo->prepare("
                    SELECT id FROM treinamento_participantes
                    WHERE treinamento_id = ? AND colaborador_id = ?
                ");
                $stmt->execute([$treinamentoId, $colaboradorId]);

                if ($stmt->fetch()) {
                    $jaVinculados++;
                    continue;
                }

                // Insere vinculação
                $sql = "INSERT INTO treinamento_participantes
                        (treinamento_id, colaborador_id, status_participacao)
                        VALUES (?, ?, ?)";

                $stmt = $this->pdo->prepare($sql);
                if ($stmt->execute([$treinamentoId, $colaboradorId, $statusParticipacao])) {
                    $vinculados++;
                }
            }

            $this->pdo->commit();

            $mensagem = "$vinculados participante(s) vinculado(s) com sucesso";
            if ($jaVinculados > 0) {
                $mensagem .= ". $jaVinculados já estava(m) vinculado(s)";
            }

            return [
                'success' => true,
                'message' => $mensagem,
                'vinculados' => $vinculados,
                'ja_vinculados' => $jaVinculados
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Erro ao vincular: ' . $e->getMessage()];
        }
    }

    /**
     * Remove vinculação de participante
     */
    public function desvincular($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM treinamento_participantes WHERE id = ?");
            $stmt->execute([$id]);

            return ['success' => true, 'message' => 'Participante desvinculado com sucesso'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao desvincular: ' . $e->getMessage()];
        }
    }

    /**
     * Atualiza status de participação
     */
    public function atualizarStatus($id, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE treinamento_participantes
                SET status_participacao = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$status, $id]);

            return ['success' => true, 'message' => 'Status atualizado com sucesso'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()];
        }
    }

    /**
     * Registra check-in do participante
     */
    public function registrarCheckIn($id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE treinamento_participantes
                SET check_in_realizado = 1,
                    data_check_in = NOW(),
                    status_participacao = 'Presente',
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$id]);

            return ['success' => true, 'message' => 'Check-in registrado com sucesso'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao registrar check-in: ' . $e->getMessage()];
        }
    }

    /**
     * Registra avaliação de reação
     */
    public function registrarAvaliacao($id, $dados) {
        try {
            $sql = "UPDATE treinamento_participantes
                    SET nota_avaliacao_reacao = ?,
                        nota_avaliacao_aprendizado = ?,
                        nota_avaliacao_comportamento = ?,
                        comentario_avaliacao = ?,
                        updated_at = NOW()
                    WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $dados['nota_reacao'] ?? null,
                $dados['nota_aprendizado'] ?? null,
                $dados['nota_comportamento'] ?? null,
                $dados['comentario'] ?? null,
                $id
            ]);

            return ['success' => true, 'message' => 'Avaliação registrada com sucesso'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao registrar avaliação: ' . $e->getMessage()];
        }
    }

    /**
     * Lista participantes de um treinamento
     */
    public function listarPorTreinamento($treinamentoId) {
        $sql = "SELECT tp.*,
                c.nome as colaborador_nome,
                c.email as colaborador_email,
                c.cargo,
                c.departamento,
                c.nivel_hierarquico
                FROM treinamento_participantes tp
                JOIN colaboradores c ON tp.colaborador_id = c.id
                WHERE tp.treinamento_id = ?
                ORDER BY c.nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$treinamentoId]);
        return $stmt->fetchAll();
    }

    /**
     * Busca colaboradores disponíveis para vincular (não vinculados ainda)
     */
    public function buscarColaboradoresDisponiveis($treinamentoId, $filtros = []) {
        $where = ['c.ativo = 1'];
        $bindings = [];

        // Filtro de busca
        if (!empty($filtros['search'])) {
            $where[] = '(c.nome LIKE ? OR c.email LIKE ? OR c.cargo LIKE ?)';
            $searchTerm = "%{$filtros['search']}%";
            $bindings[] = $searchTerm;
            $bindings[] = $searchTerm;
            $bindings[] = $searchTerm;
        }

        // Filtro de nível hierárquico
        if (!empty($filtros['nivel'])) {
            $where[] = 'c.nivel_hierarquico = ?';
            $bindings[] = $filtros['nivel'];
        }

        // Filtro de departamento
        if (!empty($filtros['departamento'])) {
            $where[] = 'c.departamento LIKE ?';
            $bindings[] = "%{$filtros['departamento']}%";
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT c.*
                FROM colaboradores c
                WHERE {$whereClause}
                AND c.id NOT IN (
                    SELECT colaborador_id
                    FROM treinamento_participantes
                    WHERE treinamento_id = ?
                )
                ORDER BY c.nome ASC";

        $bindings[] = $treinamentoId;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * Busca participante por ID
     */
    public function buscarPorId($id) {
        $sql = "SELECT tp.*,
                c.nome as colaborador_nome,
                c.email as colaborador_email,
                c.cargo,
                c.departamento,
                c.nivel_hierarquico,
                t.nome as treinamento_nome
                FROM treinamento_participantes tp
                JOIN colaboradores c ON tp.colaborador_id = c.id
                JOIN treinamentos t ON tp.treinamento_id = t.id
                WHERE tp.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Busca histórico de treinamentos de um colaborador
     */
    public function historicoPorColaborador($colaboradorId) {
        $sql = "SELECT tp.*,
                t.nome as treinamento_nome,
                t.tipo,
                t.data_inicio,
                t.data_fim,
                t.carga_horaria,
                t.status as treinamento_status
                FROM treinamento_participantes tp
                JOIN treinamentos t ON tp.treinamento_id = t.id
                WHERE tp.colaborador_id = ?
                ORDER BY t.data_inicio DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$colaboradorId]);
        return $stmt->fetchAll();
    }

    /**
     * Estatísticas de participação por treinamento
     */
    public function getEstatisticasTreinamento($treinamentoId) {
        $stats = [];

        // Total de participantes
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total FROM treinamento_participantes
            WHERE treinamento_id = ?
        ");
        $stmt->execute([$treinamentoId]);
        $stats['total_participantes'] = $stmt->fetch()['total'];

        // Por status
        $stmt = $this->pdo->prepare("
            SELECT status_participacao, COUNT(*) as total
            FROM treinamento_participantes
            WHERE treinamento_id = ?
            GROUP BY status_participacao
        ");
        $stmt->execute([$treinamentoId]);
        $statusData = $stmt->fetchAll();

        foreach ($statusData as $row) {
            $stats['status_' . strtolower($row['status_participacao'])] = $row['total'];
        }

        // Check-ins realizados
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total FROM treinamento_participantes
            WHERE treinamento_id = ? AND check_in_realizado = 1
        ");
        $stmt->execute([$treinamentoId]);
        $stats['total_checkins'] = $stmt->fetch()['total'];

        // Avaliações realizadas
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total FROM treinamento_participantes
            WHERE treinamento_id = ? AND nota_avaliacao_reacao IS NOT NULL
        ");
        $stmt->execute([$treinamentoId]);
        $stats['total_avaliacoes'] = $stmt->fetch()['total'];

        // Média das avaliações
        $stmt = $this->pdo->prepare("
            SELECT
                AVG(nota_avaliacao_reacao) as media_reacao,
                AVG(nota_avaliacao_aprendizado) as media_aprendizado,
                AVG(nota_avaliacao_comportamento) as media_comportamento
            FROM treinamento_participantes
            WHERE treinamento_id = ?
        ");
        $stmt->execute([$treinamentoId]);
        $avaliacoes = $stmt->fetch();

        $stats['media_avaliacao_reacao'] = $avaliacoes['media_reacao'] ?? 0;
        $stats['media_avaliacao_aprendizado'] = $avaliacoes['media_aprendizado'] ?? 0;
        $stats['media_avaliacao_comportamento'] = $avaliacoes['media_comportamento'] ?? 0;

        return $stats;
    }

    /**
     * Atualiza observações do participante
     */
    public function atualizarObservacoes($id, $observacoes) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE treinamento_participantes
                SET observacoes = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$observacoes, $id]);

            return ['success' => true, 'message' => 'Observações atualizadas com sucesso'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()];
        }
    }

    /**
     * Marca certificado como emitido
     */
    public function marcarCertificadoEmitido($id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE treinamento_participantes
                SET certificado_emitido = 1,
                    data_emissao_certificado = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$id]);

            return ['success' => true, 'message' => 'Certificado marcado como emitido'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()];
        }
    }
}
