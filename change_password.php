<?php
session_start();
include 'db.php'; // Inclui o arquivo de conexão com o banco de dados

// Verifica se o usuário está logado e se a senha foi enviada
if (!isset($_SESSION['usuario_logado']) || !isset($_POST['newPassword'])) {
    exit('Acesso negado!');
}

$id_usuario_logado = $_SESSION['usuario_logado']; // Assume que isso armazena o ID do usuário
$newPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT); // Gera um hash da nova senha

// Prepara a declaração SQL para atualizar a senha
$stmt = $conn->prepare("UPDATE ADMIN SET password = ? WHERE id = ?");
if (!$stmt) {
    exit('Erro ao preparar a consulta: ' . $conn->error);
}

$stmt->bind_param("si", $newPassword, $id_usuario_logado);
if ($stmt->execute()) {
    echo 'Senha alterada com sucesso.';
} else {
    echo 'Erro ao alterar a senha: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>
