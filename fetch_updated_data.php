<?php
require 'db.php'; // Asegure-se de que este caminho está correto

// Consulta ao banco de dados para obter os IPs mais usados
$sql = "SELECT dados_cgnat FROM LOGS_CGNAT";
$result = $conn->query($sql);

$ipCounts = [];

// Processamento dos dados
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        preg_match_all('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $row['dados_cgnat'], $matches);
        foreach ($matches[0] as $ip) {
            if (!isset($ipCounts[$ip])) {
                $ipCounts[$ip] = 0;
            }
            $ipCounts[$ip]++;
        }
    }
}

arsort($ipCounts); // Ordena os IPs por contagem, do maior para o menor

$topIPs = array_slice($ipCounts, 0, 10, true); // Pega os 10 IPs mais usados

// Fecha a consulta ao banco de dados
$conn->close();

// Preparando os dados para o retorno
$labels = array_keys($topIPs);
$data = array_values($topIPs);

$response = [
    'labels' => $labels,
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response);
?>
