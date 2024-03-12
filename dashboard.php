<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado. Se não, redireciona para a página de login.
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

// Inclui o arquivo de conexão com o banco de dados
require 'db.php';

$usuario_nome = 'Usuário'; // Valor padrão para o nome de usuário

// Busca o nome de usuário pelo ID armazenado na sessão
if (isset($_SESSION['usuario_logado'])) {
    $id_usuario_logado = $_SESSION['usuario_logado'];

    $stmt = $conn->prepare("SELECT username FROM ADMIN WHERE id = ?");
    if (!$stmt) {
        die("Erro ao preparar consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id_usuario_logado);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usuario_nome = $row['username']; // Atualiza com o nome de usuário real
    }
    $stmt->close();
}

// Prepara a lista de usuários para o modal de deletar usuários
$lista_usuarios = '';
$stmt = $conn->prepare("SELECT id, username FROM ADMIN");
if (!$stmt) {
    die("Erro ao preparar consulta: " . $conn->error);
}
if (!$stmt->execute()) {
    die("Erro ao executar consulta: " . $stmt->error);
}
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lista_usuarios .= '<li class="list-group-item">' . htmlspecialchars($row['username']) .
            ' <button onclick="deleteUser(' . $row['id'] . ')" class="btn btn-sm btn-danger float-right">X</button></li>';
    }
} else {
    $lista_usuarios = '<li class="list-group-item">Nenhum usuário encontrado.</li>';
}
$stmt->close();

// Configurações de paginação
$recordsPerPage = 100;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Determina o número total de registros na tabela
$totalRowsResult = $conn->query("SELECT COUNT(*) AS total FROM LOGS_CGNAT");
$totalRows = $totalRowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $recordsPerPage);

// Consulta ao banco de dados para obter os IPs mais usados
$sql = "SELECT dados_cgnat FROM LOGS_CGNAT";
$result = $conn->query($sql);

// Array para armazenar contagens de IPs
$ipCounts = [];

// Processamento dos dados
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Extrair IPs usando expressões regulares
        preg_match_all('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $row['dados_cgnat'], $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $ip = $match[1];
            // Contar IPs
            if (!isset($ipCounts[$ip])) {
                $ipCounts[$ip] = 0;
            }
            $ipCounts[$ip]++;
        }
    }
}

// Ordenar o array por contagem
arsort($ipCounts);

// Limitar o número de IPs para exibir no gráfico
$topIPs = array_slice($ipCounts, 0, 10, true);

// Fecha a consulta ao banco de dados
$result->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Moderno</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7f6;
        }
        .navbar-custom {
            background-color: #39424e;
        }
        .navbar-brand {
            color: #ffffff !important;
        }
        .card-custom {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
            border-radius: 5px;
        }
        .card-custom:hover {
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        }
        .card-header-custom {
            background-color: #39424e;
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
  <a class="navbar-brand" href="#">DASHBOARD CGNAT</a>
</nav>

   <div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h3 class="card-title">Olá, <?php echo $usuario_nome; ?>.</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">Você está logado!</p>
                    <a href="logout.php" class="btn btn-danger">Sair</a>
                    <!-- Botão para abrir o modal de alteração de senha -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#changePasswordModal">Alterar Senha</button>
                    <!-- Botão para abrir o modal de Adicionar Usuário -->
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addUserModal">Adicionar Usuário</button>
                    <!-- Botão para abrir o modal de Deletar Usuário -->
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteUserModal">Deletar Usuário</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom mt-md-0 mt-3">
                <div class="card-header card-header-custom">
                    <h3 class="card-title">Selecionar arquivo LOG!</h3>
                </div>
                <div class="card-body">
                    <form id="fileUploadForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        <!-- Substitua o conteúdo de 'listar_log.php' conforme necessário -->
                        <?php include 'listar_log.php'; ?>
                        <button type="submit" class="btn btn-primary mt-2 btn-block">Processar Log</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <!-- Substitua a barra de progresso pela imagem GIF de carregamento -->
    <div id="progressBarContainer" style="display: none; text-align: center;">
        <img src="loading.gif" alt="Carregando..." style="width: 30%; height: 150px; margin: auto;">
    </div>
</div>




<!-- Gráfico de pizza -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">IPs Mais Usados</h3>
                </div>
                <div class="card-body">
                    <div style="width: 50%;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mais conteúdo do painel aqui -->
<div class="row mt-5">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Registros de CGNAT</h3>
            </div>
            <div class="card-body">
                <!-- Adicionando campos de busca -->
                <div class="form-row mb-3">
                    <div class="form-group col-md-4">
                        <label for="ipPortSearch">Buscar por IP + Porta</label>
                        <input type="text" class="form-control" id="ipPortSearch" placeholder="Digite IP + Porta">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="datePicker">Filtrar por Data</label>
                        <input type="date" class="form-control" id="datePicker">
                    </div>
                    <div class="form-group col-md-4">
                        <label>&nbsp;</label><br>
                        <button class="btn btn-primary" onclick="updateTable()">Filtrar</button>
                    </div>
                </div>
                <!-- Tabela de registros -->
                <div class="table-responsive">
                    <table class="table table-striped" id="cgnatRecordsTable">
                        <thead>
                            <tr>
                                <th>Data e Hora</th>
                                <th>Usuário PPPoE</th>
                                <th>IP do BRAS</th>
                                <th>Firewall Log</th>
                                <th>DADOS CGNAT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM LOGS_CGNAT ORDER BY data_hora DESC LIMIT $recordsPerPage OFFSET $offset";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    // Extrai o usuário PPPoE do campo 'dados_cgnat'
                                    preg_match('/in:<([^>]+)>/', $row['dados_cgnat'], $matches);
                                    $usuarioPPPoE = $matches[1] ?? 'Desconhecido'; // Usa 'Desconhecido' caso não encontre

                                    // Remove o usuário PPPoE do campo 'dados_cgnat' para exibição
                                    $dadosCgnat = preg_replace('/in:<([^>]+)>/', '', $row['dados_cgnat']);

                                    echo "<tr>
                                            <td>{$row['data_hora']}</td>
                                            <td>{$usuarioPPPoE}</td>
                                            <td>{$row['ip_bras']}</td>
                                            <td>{$row['firewall_log']}</td>
                                            <td>{$dadosCgnat}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Nenhum dado encontrado</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="dashboard.php?page=<?php echo $page - 1; ?>">Anterior</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="dashboard.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                        <li class="page-item"><a class="page-link" href="dashboard.php?page=<?php echo $page + 1; ?>">Próxima</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Alteração de Senha -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="changePasswordModalLabel">Alterar Senha</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="changePasswordForm" method="post" action="change_password.php">
              <div class="form-group">
                <label for="newPassword">Nova Senha</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
              </div>
              <div class="form-group">
                <label for="confirmNewPassword">Confirme a Nova Senha</label>
                <input type="password" class="form-control" id="confirmNewPassword" required>
              </div>
              <button type="submit" class="btn btn-primary">Salvar Nova Senha</button>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>

<!-- Modal de Adicionar Usuário -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserModalLabel">Adicionar Novo Usuário</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addUserForm">
          <div class="form-group">
            <label for="newUsername">Usuário</label>
            <input type="text" class="form-control" id="newUsername" name="username" required>
          </div>
          <div class="form-group">
            <label for="newUserPassword">Senha</label>
            <input type="password" class="form-control" id="newUserPassword" name="password" required>
          </div>
          <div class="form-group">
            <label for="newUserEmail">Email</label>
            <input type="email" class="form-control" id="newUserEmail" name="email" required>
          </div>
          <button type="button" class="btn btn-primary" onclick="addUser()">Adicionar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal codigo para deletar o usuario-->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteUserModalLabel">Deletar Usuário</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <?php echo $lista_usuarios; ?>
        </ul>
      </div>
    </div>
  </div>
</div>


<!-- JavaScript e jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Biblioteca de gráficos Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<!-- Script para criar o gráfico de pizza -->
<script>
    // Preparar dados para o gráfico
    var labels = <?php echo json_encode(array_keys($topIPs)); ?>;
    var data = <?php echo json_encode(array_values($topIPs)); ?>;
    
    // Desenhar o gráfico de pizza
    var ctx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

</script>




<!-- Script para o modal de alteração de senha -->
<script>
document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
  event.preventDefault();
  var newPassword = document.getElementById('newPassword').value;
  var confirmNewPassword = document.getElementById('confirmNewPassword').value;

  if (newPassword !== confirmNewPassword) {
    alert('As senhas não coincidem.');
    return;
  }

  var formData = new FormData(this);

  fetch('change_password.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    alert(data);
    $('#changePasswordModal').modal('hide');
  })
  .catch(error => {
    alert('Erro ao enviar a solicitação.');
    console.error('Erro:', error);
  });
});
</script>

<!-- Script para adicionar usuário -->
<script>
function addUser() {
  var formData = new FormData(document.getElementById('addUserForm'));

  fetch('add_user.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    alert(data); // Exibe a resposta do PHP
    $('#addUserModal').modal('hide'); // Fecha o modal
    // Recarregar a página ou atualizar a parte do usuário para mostrar o novo usuário
  })
  .catch(error => {
    console.error('Erro:', error);
    alert('Erro ao adicionar o usuário.');
  });
}
</script>

<!-- Script para deletar usuário -->
<script>
function deleteUser(userId) {
  if (confirm('Tem certeza que deseja deletar este usuário?')) {
    fetch('delete_user.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'userId=' + userId
    })
    .then(response => response.text())
    .then(data => {
      alert(data);
      window.location.reload(); // Recarrega a página para atualizar a lista de usuários
    })
    .catch(error => {
      console.error('Erro:', error);
      alert('Erro ao deletar o usuário.');
    });
  }
}
</script>


<!-- script para criar a busca dos ips -->
<script>
    function updateTable() {
        // Recupera os valores dos campos de busca
        var ipPortSearch = document.getElementById('ipPortSearch').value;
        var datePicker = document.getElementById('datePicker').value;

        // Envia uma requisição AJAX para atualizar a tabela com os filtros
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Atualiza o conteúdo da tabela com a resposta da requisição
                document.getElementById("cgnatRecordsTable").innerHTML = this.responseText;
            }
        };
        xhttp.open("POST", "atualizar_tabela.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("ipPortSearch=" + ipPortSearch + "&datePicker=" + datePicker);
    }
</script>



<!-- Scripts de Javascript progress bar -->
<script>
// Função para iniciar o upload do arquivo e atualizar a barra de progresso
$('#fileUploadForm').on('submit', function(e) {
    e.preventDefault(); // Prevenir o envio padrão do formulário
    var formData = new FormData(this);

    $.ajax({
        url: 'atualiza_bd.php', // Endereço do seu script PHP de upload
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function() {
            $('#progressBarContainer').show(); // Mostrar a barra de progresso
            $('#progressBar').width('0%'); // Resetar a barra de progresso
        },
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percentComplete = e.loaded / e.total * 100;
                    $('#progressBar').width(percentComplete + '%');
                    $('#progressBar').html(Math.round(percentComplete) + '%');
                }
            }, false);
            return xhr;
        },
        success: function() {
            $('#progressBar').html('Upload concluído, processando...');
            // Inicia o monitoramento do progresso do processamento no servidor
            updateProgress();
        },
        error: function() {
            alert('Erro no upload do arquivo.');
        }
    });
});

function updateProgress() {
        $.ajax({
            url: 'progress.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var progress = parseInt(data.progress);
                $('#progressBar').width(progress + '%');
                $('#progressBar').html(progress + '%');

                if (progress < 100) {
                    setTimeout(updateProgress, 1000);
                } else {
                    $('#progressBar').html('Processamento concluído!');
                    // Atualiza a página após 2 segundos (2000 milissegundos)
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                }
            },
            error: function() {
                alert('Erro ao obter o progresso.');
            }
        });
    }


function updateUIComponents() {
    // Chamar um script PHP via AJAX que retorna os dados atualizados para a tabela e o gráfico
    $.ajax({
        url: 'fetch_updated_data.php', // Um script PHP que retorna os dados atualizados
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Atualize a tabela e o gráfico com os dados recebidos
            // Atualizar o gráfico de pizza com os novos dados
            // ...
            // Atualizar a tabela de registros
            // ...
        },
        error: function() {
            alert('Erro ao buscar dados atualizados.');
        }
    });
}
</script>



</body>
</html>

<?php
// Fecha a conexão com o banco de dados
$conn->close();
?>
