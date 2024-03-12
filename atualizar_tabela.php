<?php
// Inclui o arquivo de conexão com o banco de dados
include 'db.php';

// Recupera os valores enviados via AJAX
$ipPortSearch = $_POST['ipPortSearch'];
$datePicker = $_POST['datePicker'];

// Constrói a consulta SQL com base nos valores recebidos
$sql = "SELECT * FROM LOGS_CGNAT WHERE dados_cgnat LIKE '%" . $conn->real_escape_string($ipPortSearch) . "%' AND data_hora >= '" . $conn->real_escape_string($datePicker . ' 00:00:00') . "' AND data_hora <= '" . $conn->real_escape_string($datePicker . ' 23:59:59') . "'";

// Execute a consulta SQL e exiba os resultados como antes
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Extrai o usuário PPPoE do campo 'dados_cgnat' e exibe os dados na tabela
        echo "<tr>";
        echo "<td>{$row['data_hora']}</td>";
        // Extrai o usuário PPPoE do campo 'dados_cgnat'
        preg_match('/in:<([^>]+)>/', $row['dados_cgnat'], $matches);
        $usuarioPPPoE = $matches[1] ?? 'Desconhecido'; // Usa 'Desconhecido' caso não encontre
        echo "<td>{$usuarioPPPoE}</td>";
        echo "<td>{$row['ip_bras']}</td>";
        echo "<td>{$row['firewall_log']}</td>";
        echo "<td>{$row['dados_cgnat']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>Nenhum dado encontrado</td></tr>";
}
?>
