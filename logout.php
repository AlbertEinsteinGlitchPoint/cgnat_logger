<?php
// Inicia a sessão
session_start();

// Desfaz todos os dados da sessão
session_unset();

// Destrói a sessão
session_destroy();

// Redireciona o usuário para a página de login
header('Location: index.php');
exit;
?>
