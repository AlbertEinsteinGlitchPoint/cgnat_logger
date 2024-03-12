<?php
session_start();
include 'db.php'; // Inclui a conexão com o banco de dados

$username = $_POST['username'];
$password = $_POST['password']; // A senha inserida pelo usuário no formulário de login

// Prepara a query de seleção
$stmt = $conn->prepare("SELECT id, username, password FROM ADMIN WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se o usuário existe
if ($user = $result->fetch_assoc()) {
    // Verifica se a senha inserida corresponde ao hash armazenado
    if (password_verify($password, $user['password'])) {
        // A senha está correta, então inicia a sessão para o usuário
        $_SESSION['usuario_logado'] = $user['id']; // Ou $user['username'], dependendo do que você quer armazenar
        header("Location: dashboard.php"); // Redireciona para a página do painel
        exit;
    } else {
        // Senha incorreta
        $_SESSION['erro_login'] = "Usuário ou senha inválidos.";
        header("Location: index.php"); // Redireciona de volta para a página de login
        exit;
    }
} else {
    // Usuário não encontrado
    $_SESSION['erro_login'] = "Usuário ou senha inválidos.";
    header("Location: index.php"); // Redireciona de volta para a página de login
    exit;
}

$stmt->close();
$conn->close();
?>
