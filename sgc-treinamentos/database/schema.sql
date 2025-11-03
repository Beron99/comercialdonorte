-- =====================================================
-- SISTEMA DE GESTÃO DE CAPACITAÇÕES (SGC)
-- Versão: 1.0
-- Data: 2025-11-03
-- =====================================================

-- Usar o database já existente no Hostinger
USE u411458227_comercial;

-- =====================================================
-- TABELA: colaboradores
-- Descrição: Armazena dados dos colaboradores/funcionários
-- =====================================================
CREATE TABLE IF NOT EXISTS colaboradores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(200) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    cpf VARCHAR(14) UNIQUE,
    nivel_hierarquico ENUM('Estratégico', 'Tático', 'Operacional') NOT NULL,
    cargo VARCHAR(100),
    departamento VARCHAR(100),
    salario DECIMAL(10,2) COMMENT 'Salário mensal para cálculo de % sobre folha',
    data_admissao DATE,
    telefone VARCHAR(20),
    ativo BOOLEAN DEFAULT 1,
    origem ENUM('local', 'wordpress') DEFAULT 'local' COMMENT 'Origem do cadastro',
    wordpress_id INT NULL COMMENT 'ID do usuário no WordPress',
    foto_perfil VARCHAR(255),
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_nivel (nivel_hierarquico),
    INDEX idx_ativo (ativo),
    INDEX idx_origem (origem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: treinamentos
-- Descrição: Cadastro dos treinamentos/capacitações
-- =====================================================
CREATE TABLE IF NOT EXISTS treinamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(250) NOT NULL COMMENT 'Campo 1: Nome do Treinamento',
    tipo ENUM('Normativos', 'Comportamentais', 'Técnicos') NOT NULL COMMENT 'Campo 2: Tipo',
    componente_pe ENUM('Clientes', 'Financeiro', 'Processos Internos', 'Aprendizagem e Crescimento') NOT NULL COMMENT 'Campo 3: Componente do P.E.',
    programa ENUM('PGR', 'Líderes em Transformação', 'Crescer', 'Gerais') NOT NULL COMMENT 'Campo 4: Programa',
    objetivo TEXT COMMENT 'Campo 5: O Que (Objetivo)',
    resultados_esperados TEXT COMMENT 'Campo 6: Resultados',
    justificativa TEXT COMMENT 'Campo 7: Por Que (Justificativa)',
    carga_horaria_total DECIMAL(5,2) COMMENT 'Carga horária total em horas',
    valor_investimento DECIMAL(10,2) DEFAULT 0 COMMENT 'Campo 11: Quanto (Valor)',
    status ENUM('Programado', 'Executado', 'Pendente', 'Cancelado') DEFAULT 'Programado' COMMENT 'Campo 12: Status',
    instrutor VARCHAR(150),
    local_padrao VARCHAR(200),
    material_didatico TEXT,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_nome (nome),
    INDEX idx_tipo (tipo),
    INDEX idx_programa (programa),
    INDEX idx_status (status),
    FULLTEXT idx_busca (nome, objetivo, resultados_esperados)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: agenda_treinamentos
-- Descrição: Agendamento de datas e horários dos treinamentos
-- Relacionamento: Um treinamento pode ter múltiplas datas/turmas
-- =====================================================
CREATE TABLE IF NOT EXISTS agenda_treinamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    treinamento_id INT NOT NULL COMMENT 'FK para treinamentos',
    data_inicio DATE NOT NULL COMMENT 'Campo 8: Quando (início)',
    data_fim DATE NOT NULL COMMENT 'Campo 8: Quando (fim)',
    hora_inicio TIME COMMENT 'Horário de início',
    hora_fim TIME COMMENT 'Horário de término',
    carga_horaria_dia DECIMAL(4,2) COMMENT 'Horas deste dia específico',
    local VARCHAR(200),
    instrutor VARCHAR(150),
    vagas_disponiveis INT,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (treinamento_id) REFERENCES treinamentos(id) ON DELETE CASCADE,
    INDEX idx_data (data_inicio, data_fim),
    INDEX idx_treinamento (treinamento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: treinamento_participantes
-- Descrição: Vinculação de colaboradores aos treinamentos
-- Campo 9: Quem (Participantes)
-- =====================================================
CREATE TABLE IF NOT EXISTS treinamento_participantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    treinamento_id INT NOT NULL COMMENT 'FK para treinamentos',
    colaborador_id INT NOT NULL COMMENT 'FK para colaboradores',
    status_participacao ENUM('Confirmado', 'Pendente', 'Ausente', 'Presente', 'Cancelado') DEFAULT 'Pendente',
    check_in_realizado BOOLEAN DEFAULT 0 COMMENT 'Campo 10: Check-in',
    data_check_in TIMESTAMP NULL,
    nota_avaliacao_reacao DECIMAL(3,1) COMMENT 'Avaliação de reação (0-10)',
    nota_avaliacao_aprendizado DECIMAL(3,1) COMMENT 'Avaliação de aprendizado (0-10)',
    nota_avaliacao_comportamento DECIMAL(3,1) COMMENT 'Avaliação de mudança de comportamento (0-10)',
    comentario_avaliacao TEXT,
    certificado_emitido BOOLEAN DEFAULT 0,
    data_emissao_certificado TIMESTAMP NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (treinamento_id) REFERENCES treinamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (colaborador_id) REFERENCES colaboradores(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participacao (treinamento_id, colaborador_id),
    INDEX idx_status (status_participacao),
    INDEX idx_checkin (check_in_realizado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: frequencia_treinamento
-- Descrição: Controle detalhado de presença por dia/período
-- Campo 10: Frequência de Participantes (detalhamento)
-- =====================================================
CREATE TABLE IF NOT EXISTS frequencia_treinamento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    participante_id INT NOT NULL COMMENT 'FK para treinamento_participantes',
    agenda_id INT NOT NULL COMMENT 'FK para agenda_treinamentos (dia específico)',
    presente BOOLEAN DEFAULT 0,
    horas_participadas DECIMAL(5,2) COMMENT 'Horas efetivas de participação',
    justificativa_ausencia TEXT,
    observacoes TEXT,
    registrado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    registrado_por VARCHAR(100) COMMENT 'Usuário que registrou a frequência',

    FOREIGN KEY (participante_id) REFERENCES treinamento_participantes(id) ON DELETE CASCADE,
    FOREIGN KEY (agenda_id) REFERENCES agenda_treinamentos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_frequencia (participante_id, agenda_id),
    INDEX idx_presente (presente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: notificacoes
-- Descrição: Controle de notificações enviadas aos participantes
-- Campo 10: Sistema de notificações
-- =====================================================
CREATE TABLE IF NOT EXISTS notificacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    participante_id INT NOT NULL COMMENT 'FK para treinamento_participantes',
    tipo ENUM('convite', 'lembrete', 'confirmacao', 'certificado', 'avaliacao') NOT NULL,
    email_enviado BOOLEAN DEFAULT 0,
    data_envio TIMESTAMP NULL,
    token_check_in VARCHAR(100) UNIQUE COMMENT 'Token único para check-in',
    expiracao_token TIMESTAMP NULL,
    assunto VARCHAR(200),
    corpo_email TEXT,
    tentativas_envio INT DEFAULT 0,
    erro_envio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (participante_id) REFERENCES treinamento_participantes(id) ON DELETE CASCADE,
    INDEX idx_tipo (tipo),
    INDEX idx_enviado (email_enviado),
    INDEX idx_token (token_check_in)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: wp_sync_log
-- Descrição: Log de sincronizações com WordPress
-- =====================================================
CREATE TABLE IF NOT EXISTS wp_sync_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    total_usuarios_wp INT COMMENT 'Total de usuários no WordPress',
    novos_importados INT COMMENT 'Novos colaboradores importados',
    atualizados INT COMMENT 'Colaboradores atualizados',
    erros INT COMMENT 'Quantidade de erros',
    detalhes_erros TEXT COMMENT 'Detalhes dos erros ocorridos',
    tempo_execucao DECIMAL(6,2) COMMENT 'Tempo de execução em segundos',
    executado_por VARCHAR(100),
    data_sync TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_data (data_sync)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: configuracoes
-- Descrição: Configurações do sistema
-- =====================================================
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descricao VARCHAR(255),
    tipo ENUM('texto', 'numero', 'boolean', 'json') DEFAULT 'texto',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: usuarios_sistema
-- Descrição: Usuários do sistema SGC (administradores/gestores RH)
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios_sistema (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL COMMENT 'Hash da senha',
    nivel_acesso ENUM('admin', 'gestor', 'instrutor', 'visualizador') DEFAULT 'visualizador',
    ativo BOOLEAN DEFAULT 1,
    ultimo_acesso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_nivel (nivel_acesso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERIR CONFIGURAÇÕES PADRÃO
-- =====================================================
INSERT IGNORE INTO configuracoes (chave, valor, descricao, tipo) VALUES
    ('wp_api_url', '', 'URL da API do WordPress', 'texto'),
    ('wp_api_user', '', 'Usuário da API WordPress', 'texto'),
    ('wp_api_password', '', 'Senha de Aplicação WordPress', 'texto'),
    ('smtp_host', '', 'Servidor SMTP para envio de e-mails', 'texto'),
    ('smtp_port', '587', 'Porta SMTP', 'numero'),
    ('smtp_user', '', 'Usuário SMTP', 'texto'),
    ('smtp_password', '', 'Senha SMTP', 'texto'),
    ('email_remetente', 'noreply@comercialdonorte.com', 'E-mail remetente do sistema', 'texto'),
    ('nome_remetente', 'Sistema de Capacitações', 'Nome do remetente', 'texto'),
    ('sincronizacao_auto', 'false', 'Sincronização automática com WordPress', 'boolean'),
    ('horas_meta_anual', '40', 'Meta de horas de treinamento por colaborador/ano', 'numero'),
    ('percentual_meta_folha', '2.0', 'Meta de % investimento sobre folha salarial', 'numero');

-- =====================================================
-- INSERIR USUÁRIO ADMINISTRADOR PADRÃO
-- Senha: admin123 (hash gerado com password_hash)
-- =====================================================
INSERT IGNORE INTO usuarios_sistema (nome, email, senha, nivel_acesso) VALUES
    ('Administrador', 'admin@sgc.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =====================================================
-- VIEWS PARA RELATÓRIOS
-- =====================================================

-- View: Resumo de Treinamentos por Status
CREATE OR REPLACE VIEW vw_treinamentos_status AS
SELECT
    status,
    COUNT(*) as total,
    SUM(valor_investimento) as investimento_total,
    SUM(carga_horaria_total) as horas_totais
FROM treinamentos
GROUP BY status;

-- View: Participações por Colaborador
CREATE OR REPLACE VIEW vw_participacoes_colaborador AS
SELECT
    c.id,
    c.nome,
    c.nivel_hierarquico,
    COUNT(tp.id) as total_treinamentos,
    SUM(CASE WHEN tp.status_participacao = 'Presente' THEN 1 ELSE 0 END) as treinamentos_concluidos,
    COALESCE(SUM(f.horas_participadas), 0) as horas_totais_treinamento
FROM colaboradores c
LEFT JOIN treinamento_participantes tp ON c.id = tp.colaborador_id
LEFT JOIN frequencia_treinamento f ON tp.id = f.participante_id
WHERE c.ativo = 1
GROUP BY c.id, c.nome, c.nivel_hierarquico;

-- View: Indicadores Mensais
CREATE OR REPLACE VIEW vw_indicadores_mensais AS
SELECT
    YEAR(at.data_inicio) as ano,
    MONTH(at.data_inicio) as mes,
    COUNT(DISTINCT t.id) as total_treinamentos,
    COUNT(DISTINCT tp.colaborador_id) as total_participantes,
    SUM(t.valor_investimento) as investimento_total,
    SUM(f.horas_participadas) as horas_totais,
    AVG(tp.nota_avaliacao_reacao) as media_avaliacao
FROM agenda_treinamentos at
JOIN treinamentos t ON at.treinamento_id = t.id
JOIN treinamento_participantes tp ON t.id = tp.treinamento_id
LEFT JOIN frequencia_treinamento f ON tp.id = f.participante_id
WHERE t.status = 'Executado'
GROUP BY YEAR(at.data_inicio), MONTH(at.data_inicio);

-- =====================================================
-- ÍNDICES ADICIONAIS PARA PERFORMANCE
-- =====================================================

-- Índices compostos para queries frequentes
CREATE INDEX idx_treinamento_status_data ON treinamentos(status, created_at);
CREATE INDEX idx_participante_status ON treinamento_participantes(status_participacao, colaborador_id);
CREATE INDEX idx_frequencia_agenda_presente ON frequencia_treinamento(agenda_id, presente);
CREATE INDEX idx_agenda_periodo ON agenda_treinamentos(data_inicio, data_fim, treinamento_id);

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================
