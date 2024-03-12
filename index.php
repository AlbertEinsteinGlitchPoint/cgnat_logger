<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background-image: url('datacenter_background.jpg'); /* Adiciona a imagem de fundo */
            background-size: cover; /* Ajusta a imagem de fundo para cobrir toda a tela */
            background-position: center center; /* Centraliza a imagem de fundo */
            background-repeat: no-repeat; /* Evita a repetição da imagem de fundo */
            background-attachment: fixed; /* Fixa a imagem de fundo para que ela não role com a página */
            height: 100vh; /* Assegura que o corpo tenha altura total da tela */
            margin: 0; /* Remove a margem padrão */
            padding: 0; /* Remove o preenchimento padrão */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 350px;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: calc(100% - 20px);
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            margin-top: 10px;
            cursor: pointer;
        }

        .login-form button:hover {
            background-color: #0056b3;
        }

        .login-form h2 {
            margin-bottom: 20px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <?php
    // Inicia a sessão
    session_start();

    // Verifica se existe uma mensagem de erro para exibir
    if (isset($_SESSION['erro_login'])) {
        echo '<div style="color:red;">' . $_SESSION['erro_login'] . '</div>';
        // Limpa a mensagem de erro após exibição
        unset($_SESSION['erro_login']);
    }
    ?>

    <div class="login-container">
        <form action="authenticate.php" method="post" class="login-form">
            <h2>Login</h2>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p><a href="esqueceu_senha.php">Esqueceu a senha?</a></p>
    </div>

</body>
</html>
