<?php
/**
 * Teste de Conexão com Banco de Dados
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/classes/Database.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Conexão - <?php echo APP_NAME; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 { color: #333; margin-bottom: 30px; }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Teste de Conexão</h1>

        <?php
        try {
            // Tenta conectar
            $db = Database::getInstance();
            $conn = $db->getConnection();

            if ($db->isConnected()) {
                echo '<div class="status success">';
                echo '<h3>✅ Conexão Estabelecida com Sucesso!</h3>';
                echo '</div>';

                // Informações da conexão
                echo '<div class="info">';
                echo '<h4>📋 Informações da Conexão</h4>';
                echo '<table>';
                echo '<tr><th>Host</th><td>' . DB_HOST . '</td></tr>';
                echo '<tr><th>Database</th><td>' . DB_NAME . '</td></tr>';
                echo '<tr><th>Username</th><td>' . DB_USER . '</td></tr>';
                echo '<tr><th>Charset</th><td>' . DB_CHARSET . '</td></tr>';
                echo '<tr><th>PHP Version</th><td>' . phpversion() . '</td></tr>';
                echo '<tr><th>PDO Version</th><td>' . $conn->getAttribute(PDO::ATTR_CLIENT_VERSION) . '</td></tr>';
                echo '</table>';
                echo '</div>';

                // Testa consulta
                echo '<div class="info">';
                echo '<h4>🔍 Testando Consulta SQL</h4>';
                $stmt = $conn->query("SELECT VERSION() as version, NOW() as now");
                $result = $stmt->fetch();
                echo '<table>';
                echo '<tr><th>MySQL Version</th><td>' . $result['version'] . '</td></tr>';
                echo '<tr><th>Server Time</th><td>' . $result['now'] . '</td></tr>';
                echo '</table>';
                echo '</div>';

                // Verifica tabelas
                echo '<div class="info">';
                echo '<h4>📊 Tabelas do Sistema</h4>';
                $stmt = $conn->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (count($tables) > 0) {
                    echo '<p><strong>' . count($tables) . ' tabelas encontradas:</strong></p>';
                    echo '<ul style="columns: 2; column-gap: 20px;">';
                    foreach ($tables as $table) {
                        // Conta registros na tabela
                        try {
                            $stmtCount = $conn->query("SELECT COUNT(*) as total FROM `{$table}`");
                            $count = $stmtCount->fetch();
                            echo '<li>' . $table . ' <span style="color: #666;">(' . $count['total'] . ' registros)</span></li>';
                        } catch (Exception $e) {
                            echo '<li>' . $table . '</li>';
                        }
                    }
                    echo '</ul>';
                } else {
                    echo '<p style="color: #856404;">⚠️ Nenhuma tabela encontrada. Execute a instalação primeiro.</p>';
                    echo '<a href="install.php" class="btn">🚀 Executar Instalação</a>';
                }
                echo '</div>';

            } else {
                throw new Exception("Não foi possível estabelecer conexão");
            }

        } catch (Exception $e) {
            echo '<div class="status error">';
            echo '<h3>❌ Erro de Conexão</h3>';
            echo '<p><strong>Mensagem:</strong> ' . $e->getMessage() . '</p>';
            echo '<p><strong>Verifique:</strong></p>';
            echo '<ul>';
            echo '<li>As credenciais do banco de dados em <code>app/config/database.php</code></li>';
            echo '<li>Se o servidor MySQL está rodando</li>';
            echo '<li>Se o usuário tem permissões adequadas</li>';
            echo '<li>Se o banco de dados existe</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>

        <div style="margin-top: 30px; text-align: center;">
            <a href="install.php" class="btn">🚀 Instalação</a>
            <a href="index.php" class="btn">🏠 Página Inicial</a>
        </div>
    </div>
</body>
</html>
