<?php
/**
 * Teste Simples de Conexão MySQL
 * Use este arquivo para testar as credenciais do banco
 */

echo "<h1>🔍 Teste de Conexão MySQL</h1>";
echo "<hr>";

// Credenciais atuais
$host = 'localhost';
$dbname = 'u411458227_comercial';
$user = 'u411458227_comercial25';
$pass = '#Ide@2k25';

echo "<h3>📋 Credenciais sendo testadas:</h3>";
echo "<pre>";
echo "Host:     {$host}\n";
echo "Database: {$dbname}\n";
echo "Username: {$user}\n";
echo "Password: " . str_repeat('*', strlen($pass)) . "\n";
echo "</pre>";
echo "<hr>";

// Teste 1: Extensão MySQLi
echo "<h3>Teste 1: MySQLi</h3>";
$mysqli = @new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_error) {
    echo "❌ <strong>FALHOU</strong><br>";
    echo "Erro: " . $mysqli->connect_error . "<br>";
    echo "Código: " . $mysqli->connect_errno . "<br>";
} else {
    echo "✅ <strong>SUCESSO!</strong> Conexão MySQLi funcionando!<br>";
    echo "Versão MySQL: " . $mysqli->server_info . "<br>";
    $mysqli->close();
}
echo "<hr>";

// Teste 2: PDO
echo "<h3>Teste 2: PDO</h3>";
try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ <strong>SUCESSO!</strong> Conexão PDO funcionando!<br>";

    // Testa query
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db, USER() as user");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<pre>";
    echo "MySQL Version: " . $result['version'] . "\n";
    echo "Database Atual: " . $result['db'] . "\n";
    echo "Usuário Conectado: " . $result['user'] . "\n";
    echo "</pre>";

    // Lista tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($tables) > 0) {
        echo "<strong>Tabelas encontradas (" . count($tables) . "):</strong><br>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>{$table}</li>";
        }
        echo "</ul>";
    } else {
        echo "<strong>⚠️ Nenhuma tabela encontrada no banco.</strong><br>";
        echo "Execute a instalação após corrigir a conexão.<br>";
    }

} catch (PDOException $e) {
    echo "❌ <strong>FALHOU</strong><br>";
    echo "Erro: " . $e->getMessage() . "<br>";
    echo "Código: " . $e->getCode() . "<br>";
}
echo "<hr>";

// Instruções
echo "<h3>📝 Próximos Passos:</h3>";
echo "<ol>";
echo "<li>Se ambos os testes falharam: <strong>Verifique as credenciais no Hostinger</strong></li>";
echo "<li>Se MySQLi funcionou mas PDO falhou: <strong>Problema com extensão PDO</strong></li>";
echo "<li>Se ambos funcionaram: <strong>Execute install.php novamente</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<a href='install.php' style='padding: 10px 20px; background: #0066cc; color: white; text-decoration: none; border-radius: 5px;'>🚀 Ir para Instalação</a> ";
echo "<a href='test_connection.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>🔌 Teste Completo</a>";

?>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 900px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f6fa;
    }
    h1 { color: #333; }
    h3 {
        color: #667eea;
        background: white;
        padding: 10px;
        border-left: 4px solid #667eea;
    }
    pre {
        background: #2d2d2d;
        color: #f8f8f2;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto;
    }
</style>
