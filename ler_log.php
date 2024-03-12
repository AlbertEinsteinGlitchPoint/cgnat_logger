<?php
include 'db.php'; // Seu script de conexão ao banco de dados

$logFilePath = '/var/log/mikrotik.log';

// Abre o arquivo de log
$logFile = fopen($logFilePath, 'r');

if ($logFile) {
    while (($logLine = fgets($logFile)) !== false) {
        // Processa a linha para extrair informações necessárias
        // Exemplo: $ipPublico, $porta, $ipPrivado, $dataHora
        // Nota: Você precisará ajustar a lógica de extração com base no formato do seu log

        // Prepara a inserção no banco de dados
        $sql = "INSERT INTO LOGS_CGNAT (ip_publico, porta, ip_privado, data_hora) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $ipPublico, $porta, $ipPrivado, $dataHora);

            // Executa a inserção
            $stmt->execute();
            
            // Fecha o statement
            $stmt->close();
        }
    }

    // Fecha o arquivo de log
    fclose($logFile);
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
