<?php
/**
 * Layout: Footer
 * Rodapé padrão do sistema
 */
?>
            </div><!-- Fecha content-wrapper -->

            <footer class="footer">
                <div class="footer-content">
                    <div class="footer-left">
                        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> - Versão <?php echo APP_VERSION; ?></p>
                        <p class="footer-small">Desenvolvido por <strong>Comercial do Norte</strong></p>
                    </div>
                    <div class="footer-right">
                        <a href="<?php echo BASE_URL; ?>ajuda.php">Ajuda</a>
                        <a href="<?php echo BASE_URL; ?>documentacao.php">Documentação</a>
                        <a href="<?php echo BASE_URL; ?>suporte.php">Suporte</a>
                    </div>
                </div>
            </footer>

        </div><!-- Fecha main-content -->
    </div><!-- Fecha wrapper -->

    <!-- Scripts do sistema -->
    <script src="<?php echo ASSETS_URL; ?>js/main.js"></script>

    <!-- Scripts adicionais específicos da página -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <style>
        .footer {
            background: white;
            padding: 20px 30px;
            margin-top: 40px;
            border-top: 1px solid #e1e8ed;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
        }

        .footer-left p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .footer-small {
            font-size: 12px !important;
            color: #999 !important;
        }

        .footer-right {
            display: flex;
            gap: 20px;
        }

        .footer-right a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .footer-right a:hover {
            color: #5568d3;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .footer-right {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

    <script>
        // Auto-remove alertas após 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>
