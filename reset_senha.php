<?php
include 'db.php'; // Conexão com o banco de dados

// Inicia a sessão
session_start();

// Verifica se o e-mail foi enviado
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Verifica se o e-mail existe no banco de dados
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Usuário encontrado, gere um token de redefinição de senha
        // NOTA: Este é um exemplo, você deve gerar um token seguro e armazená-lo no banco de dados
        $token = bin2hex(random_bytes(16));

        // Aqui você deveria armazenar o token no banco de dados associado ao usuário

        // Construa o link de redefinição de senha
        $resetLink = "http://seusite.com/redefinir_senha.php?email=" . urlencode($email) . "&token=" . $token;

        // Envie o e-mail
        $to = $email;
        $subject = "Redefinição de Senha";
        $message = "Para redefinir sua senha, por favor, clique no seguinte link: " . $resetLink;
        $headers = "From: seuemail@seusite.com";

        if (mail($to, $subject, $message, $headers)) {
            echo "Instruções de redefinição de senha foram enviadas para o seu e-mail.";
        } else {
            echo "Falha ao enviar o e-mail de redefinição de senha.";
        }
    } else {
        echo "Nenhum usuário encontrado com esse e-mail.";
    }

    $stmt->close();
} else {
    echo "Por favor, forneça um e-mail.";
}

$conn->close();
?>
