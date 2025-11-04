<?php
/**
 * Debug: Verifica estrutura e dados da tabela treinamentos
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/classes/Database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "<h2>Debug: Tabela Treinamentos</h2>";

// 1. Verifica estrutura da tabela
echo "<h3>1. Estrutura da Tabela:</h3>";
echo "<pre>";
$stmt = $pdo->query("DESCRIBE treinamentos");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo sprintf("%-30s | %-20s | %-10s\n",
        $col['Field'],
        $col['Type'],
        $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
    );
}
echo "</pre>";

// 2. Verifica se coluna status existe
echo "<h3>2. Coluna Status:</h3>";
$hasStatus = false;
foreach ($columns as $col) {
    if ($col['Field'] === 'status') {
        $hasStatus = true;
        echo "<p style='color: green;'>✅ Coluna 'status' existe!</p>";
        echo "<p>Tipo: <strong>{$col['Type']}</strong></p>";
        echo "<p>Padrão: <strong>{$col['Default']}</strong></p>";
        break;
    }
}

if (!$hasStatus) {
    echo "<p style='color: red;'>❌ Coluna 'status' NÃO EXISTE!</p>";
    echo "<p>Execute a migração: database/migration_treinamentos_update.sql</p>";
}

// 3. Busca registros e mostra status
echo "<h3>3. Registros na Tabela:</h3>";
$stmt = $pdo->query("SELECT id, nome, tipo, status, data_inicio FROM treinamentos LIMIT 10");
$treinamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($treinamentos)) {
    echo "<p>Nenhum registro encontrado</p>";
} else {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Status</th><th>Data Início</th></tr>";
    foreach ($treinamentos as $t) {
        $statusColor = empty($t['status']) ? 'red' : 'green';
        echo "<tr>";
        echo "<td>{$t['id']}</td>";
        echo "<td>{$t['nome']}</td>";
        echo "<td>{$t['tipo']}</td>";
        echo "<td style='color: {$statusColor};'>" . ($t['status'] ?? '<em>NULL</em>') . "</td>";
        echo "<td>{$t['data_inicio']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 4. Contagem de status
echo "<h3>4. Distribuição de Status:</h3>";
$stmt = $pdo->query("
    SELECT
        status,
        COUNT(*) as total
    FROM treinamentos
    GROUP BY status
");
$statusCount = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Status</th><th>Total</th></tr>";
foreach ($statusCount as $s) {
    $statusValue = $s['status'] ?? '<em>NULL</em>';
    echo "<tr><td>{$statusValue}</td><td>{$s['total']}</td></tr>";
}
echo "</table>";

// 5. Sugestão de correção
echo "<h3>5. Correção:</h3>";
if ($hasStatus) {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM treinamentos WHERE status IS NULL OR status = ''");
    $nullCount = $stmt->fetch()['total'];

    if ($nullCount > 0) {
        echo "<p style='color: orange;'>⚠️ Existem {$nullCount} registros com status NULL ou vazio</p>";
        echo "<p>Execute este SQL para corrigir:</p>";
        echo "<pre>UPDATE treinamentos SET status = 'Programado' WHERE status IS NULL OR status = '';</pre>";
    } else {
        echo "<p style='color: green;'>✅ Todos os registros têm status definido!</p>";
    }
}
?>
