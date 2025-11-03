<?php
/**
 * Script de Migração: Atualizar tabela treinamentos
 * Execute este arquivo apenas UMA VEZ para adicionar as colunas necessárias
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/classes/Database.php';

echo "=================================================\n";
echo "MIGRAÇÃO: Atualizar tabela treinamentos\n";
echo "=================================================\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    echo "✓ Conexão com banco de dados estabelecida\n\n";

    // Inicia transação
    $pdo->beginTransaction();

    echo "Executando alterações na tabela...\n\n";

    // 1. Adicionar coluna fornecedor
    echo "1. Adicionando coluna 'fornecedor'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos ADD COLUMN fornecedor VARCHAR(200) COMMENT 'Fornecedor/instituição (para treinamentos externos)' AFTER tipo");
        echo " ✓\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo " ⊘ (já existe)\n";
        } else {
            throw $e;
        }
    }

    // 2. Adicionar coluna carga_horaria
    echo "2. Adicionando coluna 'carga_horaria'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos ADD COLUMN carga_horaria DECIMAL(5,2) COMMENT 'Carga horária principal' AFTER instrutor");
        echo " ✓\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo " ⊘ (já existe)\n";
        } else {
            throw $e;
        }
    }

    // 3. Adicionar coluna carga_horaria_complementar
    echo "3. Adicionando coluna 'carga_horaria_complementar'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos ADD COLUMN carga_horaria_complementar DECIMAL(5,2) COMMENT 'Carga horária complementar' AFTER carga_horaria");
        echo " ✓\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo " ⊘ (já existe)\n";
        } else {
            throw $e;
        }
    }

    // 4. Adicionar coluna data_inicio
    echo "4. Adicionando coluna 'data_inicio'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos ADD COLUMN data_inicio DATE COMMENT 'Data de início do treinamento' AFTER carga_horaria_complementar");
        echo " ✓\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo " ⊘ (já existe)\n";
        } else {
            throw $e;
        }
    }

    // 5. Adicionar coluna data_fim
    echo "5. Adicionando coluna 'data_fim'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos ADD COLUMN data_fim DATE COMMENT 'Data de término do treinamento' AFTER data_inicio");
        echo " ✓\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo " ⊘ (já existe)\n";
        } else {
            throw $e;
        }
    }

    // 6. Adicionar coluna custo_total
    echo "6. Adicionando coluna 'custo_total'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos ADD COLUMN custo_total DECIMAL(10,2) COMMENT 'Custo total do treinamento' AFTER data_fim");
        echo " ✓\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo " ⊘ (já existe)\n";
        } else {
            throw $e;
        }
    }

    // 7. Adicionar coluna origem
    echo "7. Adicionando coluna 'origem'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos ADD COLUMN origem VARCHAR(20) DEFAULT 'local' COMMENT 'Origem do cadastro' AFTER observacoes");
        echo " ✓\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo " ⊘ (já existe)\n";
        } else {
            throw $e;
        }
    }

    // 8. Atualizar ENUM tipo
    echo "8. Atualizando valores de 'tipo'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos MODIFY COLUMN tipo ENUM('Interno', 'Externo', 'Normativos', 'Comportamentais', 'Técnicos') NOT NULL COMMENT 'Tipo do treinamento'");
        echo " ✓\n";
    } catch (PDOException $e) {
        echo " ⊘ (erro: " . $e->getMessage() . ")\n";
    }

    // 9. Atualizar ENUM status
    echo "9. Atualizando valores de 'status'...";
    try {
        $pdo->exec("ALTER TABLE treinamentos MODIFY COLUMN status ENUM('Programado', 'Em Andamento', 'Executado', 'Pendente', 'Cancelado') DEFAULT 'Programado' COMMENT 'Status do treinamento'");
        echo " ✓\n";
    } catch (PDOException $e) {
        echo " ⊘ (erro: " . $e->getMessage() . ")\n";
    }

    // 10. Tornar campos opcionais
    echo "10. Tornando 'componente_pe' e 'programa' opcionais...";
    try {
        $pdo->exec("ALTER TABLE treinamentos MODIFY COLUMN componente_pe ENUM('Clientes', 'Financeiro', 'Processos Internos', 'Aprendizagem e Crescimento') NULL");
        $pdo->exec("ALTER TABLE treinamentos MODIFY COLUMN programa ENUM('PGR', 'Líderes em Transformação', 'Crescer', 'Gerais') NULL");
        echo " ✓\n";
    } catch (PDOException $e) {
        echo " ⊘ (erro: " . $e->getMessage() . ")\n";
    }

    // Confirma transação
    $pdo->commit();

    echo "\n=================================================\n";
    echo "✓ MIGRAÇÃO CONCLUÍDA COM SUCESSO!\n";
    echo "=================================================\n\n";
    echo "A tabela 'treinamentos' foi atualizada com os novos campos.\n";
    echo "Agora você pode cadastrar treinamentos normalmente.\n\n";

} catch (Exception $e) {
    // Desfaz transação em caso de erro
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "\n❌ ERRO NA MIGRAÇÃO:\n";
    echo $e->getMessage() . "\n\n";
    echo "A migração foi revertida. Nenhuma alteração foi feita no banco.\n\n";
    exit(1);
}
