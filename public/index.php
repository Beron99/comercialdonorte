<?php
/**
 * Página de Login do Sistema
 * Sistema de Gestão de Capacitações (SGC)
 */

// Define constante do sistema
define('SGC_SYSTEM', true);

// Carrega configurações
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/classes/Database.php';
require_once __DIR__ . '/../app/classes/Auth.php';

// Se já estiver logado, redireciona para dashboard
if (Auth::isLogged()) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';
$sucesso = '';

// Processa login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Valida CSRF token
    if (!isset($_POST['csrf_token']) || !csrf_validate($_POST['csrf_token'])) {
        $erro = 'Token de segurança inválido. Recarregue a página.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $erro = 'Preencha todos os campos';
        } else {
            $auth = new Auth();
            $result = $auth->login($email, $senha);

            if ($result['success']) {
                header('Location: dashboard.php');
                exit;
            } else {
                $erro = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            width: 100%;
            max-width: 420px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border-left: 4px solid #3c3;
        }

        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e1e8ed;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-size: 13px;
            margin: 0 10px;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .default-credentials {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
        }

        .default-credentials strong {
            color: #856404;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
            color: #666;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            .login-header {
                padding: 30px 20px;
            }
            .login-header h1 {
                font-size: 24px;
            }
            .login-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🎓 SGC</h1>
            <p>Sistema de Gestão de Capacitações</p>
        </div>

        <div class="login-body">
            <?php if ($erro): ?>
                <div class="alert alert-error">
                    ❌ <?php echo e($erro); ?>
                </div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="alert alert-success">
                    ✅ <?php echo e($sucesso); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                <div class="form-group">
                    <label for="email">📧 E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="seu@email.com"
                        required
                        autofocus
                        value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="senha">🔒 Senha</label>
                    <div class="password-toggle">
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="••••••••"
                            required
                        >
                        <button type="button" onclick="togglePassword()" title="Mostrar/Ocultar senha">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-login">
                    Entrar no Sistema
                </button>
            </form>

            <?php if (APP_ENV === 'development'): ?>
                <div class="default-credentials">
                    <strong>⚠️ Credenciais Padrão (Desenvolvimento):</strong><br>
                    <strong>Email:</strong> admin@sgc.com<br>
                    <strong>Senha:</strong> admin123
                </div>
            <?php endif; ?>
        </div>

        <div class="login-footer">
            <a href="test_connection.php">🔌 Testar Conexão</a>
            <a href="install.php">🚀 Instalação</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('senha');
            const button = event.currentTarget;

            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁️';
            }
        }

        // Auto-remove alertas após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
