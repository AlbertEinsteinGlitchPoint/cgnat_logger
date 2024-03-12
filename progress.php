<?php

session_start(); // Inicia a sessão para acessar as variáveis da sessão

$response = [
    'progress' => 100, // Define um valor padrão de 100% para o progresso, assumindo que o upload está concluído
    'status' => 'Concluído' // Define um status padrão de "Concluído"
];

// Verifica se a variável de sessão 'upload_progress' está definida
if (isset($_SESSION['upload_progress'])) {
    // Garante que o valor de 'upload_progress' seja um inteiro entre 0 e 100
    $progress = min(100, max(0, (int)$_SESSION['upload_progress']));
    // Atualiza o valor de progresso na resposta
    $response['progress'] = $progress;
    // Atualiza o status na resposta com base no progresso
    $response['status'] = $progress < 100 ? 'Processando' : 'Concluído';
}

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');
// Envia a resposta como JSON
echo json_encode($response);

?>
