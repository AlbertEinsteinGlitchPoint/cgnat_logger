<?php
$servername = "localhost";
$username = "seu-usuario-mysql";
$password = "sua-senha";
$dbname = "cgnat_logger";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checar conexão
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
