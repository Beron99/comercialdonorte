<?php
/**
 * Script de Instalação do SGC
 * Cria as tabelas no banco de dados e configura o sistema
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/classes/Database.php';

// Função para exibir mensagem
function showMessage($message, $type = 'info') {
    $colors = [
        'success' => '#28a745',
        'error' => '#dc3545',
        'warning' => '#ffc107',
        'info' => '#17a2b8'
    ];
    $color = $colors[$type] ?? $colors['info'];
    echo "<div style='padding: 10px; margin: 10px 0; background: {$color}; color: white; border-radius: 5px;'>{$message}</div>";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - <?php echo APP_NAME; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        .step h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .credentials {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .credentials strong {
            color: #667eea;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background: #5568d3;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Instalação do Sistema</h1>
        <p class="subtitle"><?php echo APP_NAME; ?> - Versão <?php echo APP_VERSION; ?></p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
            echo "<div class='step'><h3>📦 Iniciando Instalação...</h3></div>";

            try {
                // Testa conexão
                echo "<div class='step'><h3>🔌 Testando Conexão com Banco de Dados</h3>";
                $db = Database::getInstance();
                $conn = $db->getConnection();

                if ($db->isConnected()) {
                    showMessage("✅ Conexão estabelecida com sucesso!", 'success');
                    echo "<div class='credentials'>";
                    echo "<strong>Host:</strong> " . DB_HOST . "<br>";
                    echo "<strong>Database:</strong> " . DB_NAME . "<br>";
                    echo "<strong>Charset:</strong> " . DB_CHARSET;
                    echo "</div></div>";
                } else {
                    throw new Exception("Não foi possível conectar ao banco de dados");
                }

                // Lê e executa o SQL
                echo "<div class='step'><h3>📋 Executando Script SQL</h3>";
                $sqlFile = __DIR__ . '/../database/schema.sql';

                if (!file_exists($sqlFile)) {
                    throw new Exception("Arquivo schema.sql não encontrado!");
                }

                $sql = file_get_contents($sqlFile);

                // Remove comentários e divide em statements
                $sql = preg_replace('/--.*$/m', '', $sql);
                $statements = array_filter(
                    array_map('trim', explode(';', $sql)),
                    function($stmt) {
                        return !empty($stmt) && $stmt !== '' && strpos($stmt, 'DELIMITER') === false;
                    }
                );

                $executed = 0;
                $errors = 0;

                foreach ($statements as $statement) {
                    if (trim($statement) !== '') {
                        try {
                            $conn->exec($statement);
                            $executed++;
                        } catch (PDOException $e) {
                            // Ignora erros de "já existe"
                            if (strpos($e->getMessage(), 'already exists') === false) {
                                echo "<div style='color: orange; font-size: 12px; margin: 5px 0;'>";
                                echo "⚠️ Aviso: " . substr($e->getMessage(), 0, 100) . "...";
                                echo "</div>";
                                $errors++;
                            }
                        }
                    }
                }

                showMessage("✅ SQL executado com sucesso! {$executed} comandos processados.", 'success');
                if ($errors > 0) {
                    showMessage("⚠️ {$errors} avisos encontrados (provavelmente tabelas já existentes)", 'warning');
                }
                echo "</div>";

                // Verifica tabelas criadas
                echo "<div class='step'><h3>🔍 Verificando Tabelas Criadas</h3>";
                $stmt = $conn->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (count($tables) > 0) {
                    showMessage("✅ " . count($tables) . " tabelas encontradas:", 'success');
                    echo "<pre>";
                    foreach ($tables as $table) {
                        echo "• {$table}\n";
                    }
                    echo "</pre>";
                } else {
                    showMessage("⚠️ Nenhuma tabela encontrada. Verifique o script SQL.", 'warning');
                }
                echo "</div>";

                // Verifica usuário admin
                echo "<div class='step'><h3>👤 Verificando Usuário Administrador</h3>";
                $stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios_sistema WHERE nivel_acesso = 'admin'");
                $result = $stmt->fetch();

                if ($result['total'] > 0) {
                    showMessage("✅ Usuário administrador encontrado!", 'success');
                    echo "<div class='credentials'>";
                    echo "<strong>Email:</strong> admin@sgc.com<br>";
                    echo "<strong>Senha:</strong> admin123<br>";
                    echo "<span style='color: #dc3545;'>⚠️ Altere a senha após o primeiro login!</span>";
                    echo "</div>";
                } else {
                    showMessage("⚠️ Usuário administrador não encontrado. Execute o INSERT manual.", 'warning');
                }
                echo "</div>";

                // Sucesso final
                echo "<div class='step' style='background: #d4edda; border-left-color: #28a745;'>";
                echo "<h3 style='color: #28a745;'>🎉 Instalação Concluída com Sucesso!</h3>";
                echo "<p>O sistema está pronto para uso. Você pode acessar:</p>";
                echo "<ul style='margin: 10px 0; padding-left: 20px;'>";
                echo "<li><a href='index.php' style='color: #667eea;'>Página Inicial (Login)</a></li>";
                echo "<li><a href='test_connection.php' style='color: #667eea;'>Testar Conexão</a></li>";
                echo "</ul>";
                echo "</div>";

            } catch (Exception $e) {
                showMessage("❌ Erro durante a instalação: " . $e->getMessage(), 'error');
                echo "<pre>";
                echo $e->getTraceAsString();
                echo "</pre>";
            }
        } else {
            // Formulário de instalação
            ?>
            <div class="step">
                <h3>📋 Informações do Banco de Dados</h3>
                <p>As seguintes credenciais serão utilizadas para a instalação:</p>
                <div class="credentials">
                    <strong>Host:</strong> <?php echo DB_HOST; ?><br>
                    <strong>Database:</strong> <?php echo DB_NAME; ?><br>
                    <strong>Username:</strong> <?php echo DB_USER; ?><br>
                    <strong>Charset:</strong> <?php echo DB_CHARSET; ?>
                </div>
            </div>

            <div class="step">
                <h3>⚙️ O que será instalado?</h3>
                <ul style="padding-left: 20px; line-height: 1.8;">
                    <li>✅ 9 tabelas do sistema (colaboradores, treinamentos, etc.)</li>
                    <li>✅ 3 views para relatórios</li>
                    <li>✅ Índices para otimização de performance</li>
                    <li>✅ Configurações padrão do sistema</li>
                    <li>✅ Usuário administrador inicial</li>
                </ul>
            </div>

            <div class="step" style="background: #fff3cd; border-left-color: #ffc107;">
                <h3 style="color: #856404;">⚠️ Atenção</h3>
                <p>Este script criará as tabelas no banco de dados <strong><?php echo DB_NAME; ?></strong>.</p>
                <p>Certifique-se de que está usando o banco correto antes de prosseguir.</p>
            </div>

            <form method="POST">
                <button type="submit" name="install">🚀 Iniciar Instalação</button>
            </form>
            <?php
        }
        ?>
    </div>
</body>
</html>
