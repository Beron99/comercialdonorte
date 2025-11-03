-- =====================================================
-- MIGRAÇÃO: Atualizar estrutura da tabela treinamentos
-- Data: 2025-01-XX
-- Descrição: Ajusta campos para novo formato do módulo
-- =====================================================

-- Adicionar novas colunas necessárias
ALTER TABLE treinamentos
ADD COLUMN IF NOT EXISTS fornecedor VARCHAR(200) COMMENT 'Fornecedor/instituição (para treinamentos externos)' AFTER tipo,
ADD COLUMN IF NOT EXISTS carga_horaria DECIMAL(5,2) COMMENT 'Carga horária principal' AFTER instrutor,
ADD COLUMN IF NOT EXISTS carga_horaria_complementar DECIMAL(5,2) COMMENT 'Carga horária complementar' AFTER carga_horaria,
ADD COLUMN IF NOT EXISTS data_inicio DATE COMMENT 'Data de início do treinamento' AFTER carga_horaria_complementar,
ADD COLUMN IF NOT EXISTS data_fim DATE COMMENT 'Data de término do treinamento' AFTER data_inicio,
ADD COLUMN IF NOT EXISTS custo_total DECIMAL(10,2) COMMENT 'Custo total do treinamento' AFTER data_fim,
ADD COLUMN IF NOT EXISTS origem VARCHAR(20) DEFAULT 'local' COMMENT 'Origem do cadastro (local ou wordpress)' AFTER observacoes;

-- Atualizar tipo ENUM para novo formato (Interno/Externo)
ALTER TABLE treinamentos
MODIFY COLUMN tipo ENUM('Interno', 'Externo', 'Normativos', 'Comportamentais', 'Técnicos') NOT NULL COMMENT 'Tipo do treinamento';

-- Atualizar status ENUM para incluir 'Em Andamento'
ALTER TABLE treinamentos
MODIFY COLUMN status ENUM('Programado', 'Em Andamento', 'Executado', 'Pendente', 'Cancelado') DEFAULT 'Programado' COMMENT 'Status do treinamento';

-- Tornar campos opcionais que antes eram obrigatórios (compatibilidade)
ALTER TABLE treinamentos
MODIFY COLUMN componente_pe ENUM('Clientes', 'Financeiro', 'Processos Internos', 'Aprendizagem e Crescimento') NULL COMMENT 'Campo 3: Componente do P.E.',
MODIFY COLUMN programa ENUM('PGR', 'Líderes em Transformação', 'Crescer', 'Gerais') NULL COMMENT 'Campo 4: Programa';

-- Adicionar índices para melhor performance
ALTER TABLE treinamentos
ADD INDEX IF NOT EXISTS idx_fornecedor (fornecedor),
ADD INDEX IF NOT EXISTS idx_data_inicio (data_inicio),
ADD INDEX IF NOT EXISTS idx_data_fim (data_fim),
ADD INDEX IF NOT EXISTS idx_origem (origem);

-- =====================================================
-- COMENTÁRIOS
-- =====================================================
-- Esta migração adiciona os campos necessários para o módulo de treinamentos
-- mantendo compatibilidade com campos antigos do sistema original
--
-- Campos novos adicionados:
-- - fornecedor: Para treinamentos externos
-- - carga_horaria: Carga horária principal
-- - carga_horaria_complementar: Horas extras/complementares
-- - data_inicio: Data de início
-- - data_fim: Data de término
-- - custo_total: Investimento total
-- - origem: Origem do cadastro (local/wordpress)
--
-- Campos modificados:
-- - tipo: Adicionado 'Interno' e 'Externo'
-- - status: Adicionado 'Em Andamento'
-- - componente_pe: Tornado opcional (NULL)
-- - programa: Tornado opcional (NULL)
-- =====================================================
