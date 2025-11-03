<?php
/**
 * Redirecionamento para a pasta public
 * Este arquivo garante que o acesso à raiz redirecione para public/
 */

// Redireciona para a pasta public
header('Location: public/index.php');
exit;
