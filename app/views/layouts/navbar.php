<?php
/**
 * Layout: Navbar
 * Barra de navegação superior
 */
?>
<style>
    .navbar {
        background: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        position: sticky;
        top: 0;
        z-index: 999;
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        padding: 5px 10px;
    }

    .navbar-search {
        position: relative;
    }

    .navbar-search input {
        padding: 8px 15px 8px 40px;
        border: 2px solid #e1e8ed;
        border-radius: 20px;
        width: 300px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .navbar-search input:focus {
        outline: none;
        border-color: #667eea;
        width: 350px;
    }

    .navbar-search .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .navbar-item {
        position: relative;
        cursor: pointer;
    }

    .navbar-icon {
        font-size: 20px;
        color: #666;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .navbar-icon:hover {
        background: #f5f6fa;
        color: #667eea;
    }

    .badge {
        position: absolute;
        top: 0;
        right: 0;
        background: #dc3545;
        color: white;
        font-size: 10px;
        padding: 2px 5px;
        border-radius: 10px;
        font-weight: bold;
    }

    .user-menu {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px 15px;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .user-menu:hover {
        background: #f5f6fa;
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .user-name {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .user-role {
        font-size: 11px;
        color: #999;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        min-width: 200px;
        margin-top: 10px;
        z-index: 1000;
    }

    .dropdown-menu.show {
        display: block;
        animation: fadeInDown 0.3s;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-menu a {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s;
        border-bottom: 1px solid #f5f6fa;
    }

    .dropdown-menu a:last-child {
        border-bottom: none;
    }

    .dropdown-menu a:hover {
        background: #f5f6fa;
        padding-left: 25px;
    }

    @media (max-width: 768px) {
        .mobile-menu-btn {
            display: block;
        }

        .navbar-search {
            display: none;
        }

        .user-info {
            display: none;
        }

        .navbar-right {
            gap: 10px;
        }
    }
</style>

<nav class="navbar">
    <div class="navbar-left">
        <button class="mobile-menu-btn" onclick="toggleMobileSidebar()">☰</button>

        <div class="navbar-search">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Buscar...">
        </div>
    </div>

    <div class="navbar-right">
        <div class="navbar-item" onclick="toggleDropdown('notifications')">
            <span class="navbar-icon">🔔</span>
            <span class="badge">3</span>
            <div class="dropdown-menu" id="dropdown-notifications">
                <a href="#">Novo treinamento agendado</a>
                <a href="#">3 colaboradores confirmados</a>
                <a href="#">Relatório mensal disponível</a>
                <a href="#">Ver todas notificações</a>
            </div>
        </div>

        <div class="navbar-item" onclick="toggleDropdown('messages')">
            <span class="navbar-icon">✉️</span>
            <span class="badge">5</span>
            <div class="dropdown-menu" id="dropdown-messages">
                <a href="#">Nova mensagem de João Silva</a>
                <a href="#">Avaliação de treinamento</a>
                <a href="#">Ver todas mensagens</a>
            </div>
        </div>

        <div class="navbar-item" onclick="toggleDropdown('user')">
            <div class="user-menu">
                <div class="user-avatar">
                    <?php echo strtoupper(substr(Auth::getUserName(), 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo e(Auth::getUserName()); ?></div>
                    <div class="user-role">
                        <?php
                        $roles = [
                            'admin' => 'Administrador',
                            'gestor' => 'Gestor',
                            'instrutor' => 'Instrutor',
                            'visualizador' => 'Visualizador'
                        ];
                        echo $roles[Auth::getUserLevel()] ?? 'Usuário';
                        ?>
                    </div>
                </div>
                <span>▼</span>
            </div>
            <div class="dropdown-menu" id="dropdown-user">
                <a href="<?php echo BASE_URL; ?>perfil.php">👤 Meu Perfil</a>
                <a href="<?php echo BASE_URL; ?>perfil/editar.php">✏️ Editar Perfil</a>
                <a href="<?php echo BASE_URL; ?>perfil/senha.php">🔒 Alterar Senha</a>
                <?php if (Auth::isAdmin()): ?>
                <a href="<?php echo BASE_URL; ?>config/sistema.php">⚙️ Configurações</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>ajuda.php">❓ Ajuda</a>
                <a href="<?php echo BASE_URL; ?>logout.php" style="color: #dc3545;">🚪 Sair</a>
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleDropdown(id) {
        // Fecha todos os dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu.id !== 'dropdown-' + id) {
                menu.classList.remove('show');
            }
        });

        // Toggle do dropdown clicado
        const dropdown = document.getElementById('dropdown-' + id);
        dropdown.classList.toggle('show');
    }

    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('mobile-show');
    }

    // Fecha dropdowns ao clicar fora
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.navbar-item')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
</script>
