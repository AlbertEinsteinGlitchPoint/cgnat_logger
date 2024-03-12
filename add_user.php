<?php
include 'db.php'; // Certifique-se de que este caminho está correto

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash da senha
$email = $_POST['email'];

$stmt = $conn->prepare("INSERT INTO ADMIN (username, password, email, reg_date) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $username, $password, $email);

if ($stmt->execute()) {
    echo 'Usuário adicionado com sucesso.';
} else {
    echo 'Erro ao adicionar o usuário: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>
