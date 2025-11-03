<?php
/**
 * Script para Criar Usuário Administrador
 * Execute este arquivo para criar/recriar o usuário admin padrão
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
    <title>Criar Usuário Admin - SGC</title>
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
            max-width: 700px;
            margin: 50px auto;
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
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left-color: #ffc107;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
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
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .credentials strong {
            color: #667eea;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 10px 0;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👤 Criar Usuário Administrador</h1>
        <p class="subtitle">Sistema de Gestão de Capacitações (SGC)</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_admin'])) {
            try {
                $db = Database::getInstance();
                $pdo = $db->getConnection();

                // Verifica se usuário admin já existe
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios_sistema WHERE email = 'admin@sgc.com'");
                $result = $stmt->fetch();

                if ($result['total'] > 0) {
                    echo '<div class="alert alert-warning">';
                    echo '<strong>⚠️ Usuário já existe!</strong><br>';
                    echo 'Deseja redefinir a senha do usuário admin?';
                    echo '<form method="POST" style="margin-top: 15px;">';
                    echo '<button type="submit" name="resetar_senha">🔄 Sim, Resetar Senha</button>';
                    echo '</form>';
                    echo '</div>';
                } else {
                    // Cria hash da senha
                    $senha = 'admin123';
                    $senhaHash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);

                    // Insere usuário admin
                    $stmt = $pdo->prepare("
                        INSERT INTO usuarios_sistema (nome, email, senha, nivel_acesso, ativo)
                        VALUES (?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        'Administrador',
                        'admin@sgc.com',
                        $senhaHash,
                        'admin',
                        1
                    ]);

                    echo '<div class="alert alert-success">';
                    echo '<strong>✅ Usuário Administrador Criado com Sucesso!</strong>';
                    echo '</div>';

                    echo '<div class="credentials">';
                    echo '<h3>🔑 Credenciais de Acesso:</h3>';
                    echo '<p><strong>E-mail:</strong> admin@sgc.com</p>';
                    echo '<p><strong>Senha:</strong> admin123</p>';
                    echo '<p style="color: #dc3545; margin-top: 15px;">';
                    echo '<strong>⚠️ IMPORTANTE:</strong> Altere a senha após o primeiro login!';
                    echo '</p>';
                    echo '</div>';

                    echo '<div class="btn-group">';
                    echo '<a href="index.php" style="padding: 12px 30px; background: #28a745; color: white; border-radius: 5px; text-decoration: none;">🚀 Ir para Login</a>';
                    echo '</div>';
                }

            } catch (Exception $e) {
                echo '<div class="alert alert-error">';
                echo '<strong>❌ Erro ao criar usuário:</strong><br>';
                echo $e->getMessage();
                echo '</div>';
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resetar_senha'])) {
            try {
                $db = Database::getInstance();
                $pdo = $db->getConnection();

                // Reseta senha do admin
                $senha = 'admin123';
                $senhaHash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);

                $stmt = $pdo->prepare("
                    UPDATE usuarios_sistema
                    SET senha = ?, ativo = 1, updated_at = NOW()
                    WHERE email = 'admin@sgc.com'
                ");

                $stmt->execute([$senhaHash]);

                echo '<div class="alert alert-success">';
                echo '<strong>✅ Senha Resetada com Sucesso!</strong>';
                echo '</div>';

                echo '<div class="credentials">';
                echo '<h3>🔑 Novas Credenciais:</h3>';
                echo '<p><strong>E-mail:</strong> admin@sgc.com</p>';
                echo '<p><strong>Senha:</strong> admin123</p>';
                echo '</div>';

                echo '<div class="btn-group">';
                echo '<a href="index.php" style="padding: 12px 30px; background: #28a745; color: white; border-radius: 5px; text-decoration: none;">🚀 Ir para Login</a>';
                echo '</div>';

            } catch (Exception $e) {
                echo '<div class="alert alert-error">';
                echo '<strong>❌ Erro ao resetar senha:</strong><br>';
                echo $e->getMessage();
                echo '</div>';
            }
        } else {
            // Formulário inicial
            ?>
            <div class="alert alert-info">
                <strong>ℹ️ Sobre este script:</strong><br>
                Este script cria ou reseta o usuário administrador padrão do sistema.
            </div>

            <div class="credentials">
                <h3>🔑 Usuário que será criado:</h3>
                <p><strong>Nome:</strong> Administrador</p>
                <p><strong>E-mail:</strong> admin@sgc.com</p>
                <p><strong>Senha:</strong> admin123</p>
                <p><strong>Nível de Acesso:</strong> Administrador (admin)</p>
            </div>

            <?php
            try {
                $db = Database::getInstance();
                $pdo = $db->getConnection();

                // Verifica estado atual
                $stmt = $pdo->query("SELECT * FROM usuarios_sistema WHERE email = 'admin@sgc.com'");
                $admin = $stmt->fetch();

                if ($admin) {
                    echo '<div class="alert alert-warning">';
                    echo '<strong>⚠️ Usuário já existe no banco de dados!</strong><br>';
                    echo 'Nome: ' . htmlspecialchars($admin['nome']) . '<br>';
                    echo 'E-mail: ' . htmlspecialchars($admin['email']) . '<br>';
                    echo 'Nível: ' . htmlspecialchars($admin['nivel_acesso']) . '<br>';
                    echo 'Status: ' . ($admin['ativo'] ? '✅ Ativo' : '❌ Inativo') . '<br>';
                    echo '<br>';
                    echo 'Se você está tendo problemas para fazer login, clique no botão abaixo para resetar a senha.';
                    echo '</div>';
                }

            } catch (Exception $e) {
                echo '<div class="alert alert-error">';
                echo '<strong>❌ Erro ao verificar banco:</strong><br>';
                echo $e->getMessage();
                echo '</div>';
            }
            ?>

            <form method="POST">
                <button type="submit" name="criar_admin">
                    <?php echo isset($admin) ? '🔄 Resetar Senha do Admin' : '✅ Criar Usuário Admin'; ?>
                </button>
            </form>

            <div class="btn-group">
                <a href="test_connection.php" class="btn-secondary" style="padding: 12px 30px; background: #6c757d; color: white; border-radius: 5px; text-decoration: none;">🔌 Testar Conexão</a>
                <a href="install.php" class="btn-secondary" style="padding: 12px 30px; background: #6c757d; color: white; border-radius: 5px; text-decoration: none;">🚀 Instalação</a>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
