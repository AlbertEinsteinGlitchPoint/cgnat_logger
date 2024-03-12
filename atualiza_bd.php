<?php
set_time_limit(300); // Aumenta o limite de tempo de execução para 5 minutos

ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db.php'; // Inclui o arquivo de conexão com o banco de dados

$logDir = '/var/www/html/cgnat/logs/';
$logFileName = 'atualiza_db-' . date('Y-m-d') . '.log';
$logPath = $logDir . $logFileName;

if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

function addLog($message, $logPath) {
    file_put_contents($logPath, date('Y-m-d H:i:s') . ' - ' . $message . "\n", FILE_APPEND);
}

// Limpar dados antigos da tabela LOGS_CGNAT
if ($conn->query("TRUNCATE TABLE LOGS_CGNAT")) {
    addLog("Dados antigos excluídos com sucesso da tabela LOGS_CGNAT.", $logPath);
} else {
    addLog("Erro ao limpar os dados antigos da tabela LOGS_CGNAT: " . $conn->error, $logPath);
    exit; // Saia do script se houver um erro ao limpar os dados antigos
}

// Verifica se um nome de arquivo de log foi passado e constrói o caminho completo
if (isset($_POST['logFile'])) {
    $logFileToProcess = '/var/log/mikrotik/' . basename($_POST['logFile']);
} else {
    die('Nenhum arquivo de log especificado.');
}

$year = date('Y'); // Utiliza o ano atual
$batchSize = 1000; // Número de linhas a serem processadas em cada lote
$batchData = []; // Armazena os dados do lote atual

if ($logFile = fopen($logFileToProcess, 'r')) {
    while (!feof($logFile)) {
        $logLine = fgets($logFile);
        if (preg_match('/(\w{3} \d{2} \d{2}:\d{2}:\d{2}) (\d+\.\d+\.\d+\.\d+) firewall,info CGNAT: CGNAT forward: (.*)/', $logLine, $matches)) {
            $dateString = $matches[1] . " " . $year; // Adiciona o ano à string de data
            $dateTime = DateTime::createFromFormat('M d H:i:s Y', $dateString);
            if ($dateTime === false) {
                addLog("Erro ao criar objeto DateTime a partir da string: " . $dateString, $logPath);
                continue;
            }
            $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

            // Adiciona a linha processada ao lote atual
            $batchData[] = [
                'data_hora' => $formattedDateTime,
                'ip_bras' => $matches[2],
                'firewall_log' => 'firewall,info CGNAT: CGNAT forward:',
                'dados_cgnat' => $matches[3]
            ];

            // Se alcançarmos o tamanho do lote, insira o lote no banco de dados
            if (count($batchData) >= $batchSize) {
                insertBatchIntoDatabase($batchData, $conn, $logPath);
                $batchData = []; // Limpa o lote após a inserção
            }
        } else {
            addLog("A linha do log não corresponde ao padrão esperado.", $logPath);
        }
    }

    // Verifica se ainda há dados no lote após o fim do arquivo e os insere no banco de dados
    if (!empty($batchData)) {
        insertBatchIntoDatabase($batchData, $conn, $logPath);
    }

    fclose($logFile);
    addLog("Processamento concluído para: $logFileToProcess", $logPath);
} else {
    addLog("Não foi possível abrir o arquivo de log: $logFileToProcess", $logPath);
}

header('Location: dashboard.php'); // Redireciona para o dashboard após o processamento
exit;

// Função para inserir um lote de dados no banco de dados
function insertBatchIntoDatabase($batchData, $conn, $logPath) {
    $values = [];
    $params = [];
    $paramTypes = '';

    foreach ($batchData as $row) {
        $values[] = "(?, ?, ?, ?)";
        $params[] = $row['data_hora'];
        $params[] = $row['ip_bras'];
        $params[] = $row['firewall_log'];
        $params[] = $row['dados_cgnat'];
        $paramTypes .= 'ssss'; // 4 strings por linha
    }

    $stmt = $conn->prepare("INSERT INTO LOGS_CGNAT (data_hora, ip_bras, firewall_log, dados_cgnat) VALUES " . implode(',', $values));
    if (!$stmt) {
        addLog("Erro ao preparar a inserção: " . $conn->error, $logPath);
        return;
    }
    $stmt->bind_param($paramTypes, ...$params);
    if (!$stmt->execute()) {
        addLog("Erro ao inserir o lote no banco de dados: " . $stmt->error, $logPath);
    }
    $stmt->close();
}
