<?php
/**
 * Layout: Sidebar
 * Menu lateral do sistema
 */
?>
<style>
    .sidebar {
        width: 260px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        overflow-y: auto;
        transition: all 0.3s;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .sidebar.collapsed {
        width: 70px;
    }

    .sidebar-header {
        padding: 25px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-header h2 {
        color: white;
        font-size: 24px;
        margin-bottom: 5px;
        transition: opacity 0.3s;
    }

    .sidebar.collapsed .sidebar-header h2,
    .sidebar.collapsed .sidebar-header p {
        opacity: 0;
        display: none;
    }

    .sidebar-header p {
        color: rgba(255,255,255,0.8);
        font-size: 12px;
    }

    .sidebar-menu {
        list-style: none;
        padding: 20px 0;
    }

    .sidebar-menu li {
        margin: 5px 0;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
        position: relative;
    }

    .sidebar-menu a:hover {
        background: rgba(255,255,255,0.1);
        padding-left: 25px;
    }

    .sidebar-menu a.active {
        background: rgba(255,255,255,0.15);
        border-left: 4px solid white;
    }

    .sidebar-menu a .icon {
        font-size: 20px;
        margin-right: 15px;
        min-width: 25px;
        text-align: center;
    }

    .sidebar.collapsed .sidebar-menu a .text {
        display: none;
    }

    .sidebar.collapsed .sidebar-menu a {
        justify-content: center;
        padding: 12px 0;
    }

    .sidebar.collapsed .sidebar-menu a .icon {
        margin-right: 0;
    }

    .sidebar-menu .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s;
        background: rgba(0,0,0,0.1);
    }

    .sidebar-menu .submenu.active {
        max-height: 500px;
    }

    .sidebar-menu .submenu a {
        padding-left: 60px;
        font-size: 14px;
    }

    .sidebar-menu .submenu a:hover {
        padding-left: 65px;
    }

    .sidebar-toggle {
        position: absolute;
        right: -15px;
        top: 20px;
        width: 30px;
        height: 30px;
        background: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .sidebar {
            left: -260px;
        }

        .sidebar.mobile-show {
            left: 0;
        }
    }
</style>

<div class="sidebar" id="sidebar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

    <div class="sidebar-header">
        <h2>🎓 SGC</h2>
        <p>Sistema de Capacitações</p>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo BASE_URL; ?>dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                <span class="icon">📊</span>
                <span class="text">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>colaboradores/listar.php">
                <span class="icon">👥</span>
                <span class="text">Colaboradores</span>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>treinamentos/listar.php">
                <span class="icon">📚</span>
                <span class="text">Treinamentos</span>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>participantes/vincular.php">
                <span class="icon">✅</span>
                <span class="text">Participantes</span>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>frequencia/registrar.php">
                <span class="icon">📝</span>
                <span class="text">Frequência</span>
            </a>
        </li>

        <li>
            <a href="#" onclick="toggleSubmenu('relatorios'); return false;">
                <span class="icon">📈</span>
                <span class="text">Relatórios</span>
            </a>
            <ul class="submenu" id="submenu-relatorios">
                <li><a href="<?php echo BASE_URL; ?>relatorios/dashboard.php">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>relatorios/mensal.php">Mensal</a></li>
                <li><a href="<?php echo BASE_URL; ?>relatorios/anual.php">Anual</a></li>
                <li><a href="<?php echo BASE_URL; ?>relatorios/comparativo.php">Comparativo</a></li>
            </ul>
        </li>

        <?php if (Auth::hasLevel(['admin', 'gestor'])): ?>
        <li>
            <a href="<?php echo BASE_URL; ?>integracao/configurar.php">
                <span class="icon">🔗</span>
                <span class="text">Integração WordPress</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (Auth::isAdmin()): ?>
        <li>
            <a href="#" onclick="toggleSubmenu('configuracoes'); return false;">
                <span class="icon">⚙️</span>
                <span class="text">Configurações</span>
            </a>
            <ul class="submenu" id="submenu-configuracoes">
                <li><a href="<?php echo BASE_URL; ?>config/sistema.php">Sistema</a></li>
                <li><a href="<?php echo BASE_URL; ?>config/usuarios.php">Usuários</a></li>
                <li><a href="<?php echo BASE_URL; ?>config/email.php">E-mail</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <li style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
            <a href="<?php echo BASE_URL; ?>perfil.php">
                <span class="icon">👤</span>
                <span class="text">Meu Perfil</span>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>logout.php" onclick="return confirm('Deseja realmente sair do sistema?');">
                <span class="icon">🚪</span>
                <span class="text">Sair</span>
            </a>
        </li>
    </ul>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('sidebar-collapsed');

        // Salva preferência no localStorage
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    }

    function toggleSubmenu(id) {
        const submenu = document.getElementById('submenu-' + id);
        submenu.classList.toggle('active');
    }

    // Restaura estado do sidebar do localStorage
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed) {
            document.getElementById('sidebar').classList.add('collapsed');
            document.getElementById('mainContent').classList.add('sidebar-collapsed');
        }
    });
</script>
