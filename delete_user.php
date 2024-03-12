<?php
include 'db.php'; // Certifique-se de que este caminho está correto

$userId = $_POST['userId'];

$stmt = $conn->prepare("DELETE FROM ADMIN WHERE id = ?");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo 'Usuário deletado com sucesso.';
} else {
    echo 'Erro ao deletar usuário: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>
