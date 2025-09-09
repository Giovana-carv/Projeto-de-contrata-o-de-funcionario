<?php
// Conexão com banco
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Captura filtro de pesquisa
$filtro = isset($_GET['tipo']) ? trim($_GET['tipo']) : "";
$listarTodos = isset($_GET['listar']) ? true : false;

// Inicializa array e flag
$usuarios = [];
$mostrarTabela = false;

// Consulta somente se houver filtro ou listar todos
if ($filtro !== "" || $listarTodos) {
    if ($filtro !== "") {
        $sql  = "SELECT nome, email, tipo, ocupacao FROM usuarios WHERE tipo LIKE ? OR ocupacao LIKE ?";
        $stmt = $conn->prepare($sql);
        $like = "%{$filtro}%";
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Listar todos
        $result = $conn->query("SELECT nome, email, tipo, ocupacao FROM usuarios");
    }

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }

    // Mostrar a tabela apenas após uma ação
    $mostrarTabela = true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard de Usuários</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    body { background: #f4f6f9; font-family: 'Arial', sans-serif; text-align:center; margin:0; }
    header { background: linear-gradient(to right, #ff4b2b, #ff416c); color:#fff; padding:20px; margin-bottom:0; }

    .search-bar {
      display:flex; justify-content:center; align-items:stretch; margin:10px auto 20px;
      max-width:420px;
    }
    .search-bar input {
      border-radius:20px 0 0 20px; border:1px solid #ff4b2b;
      padding:10px 15px; width:100%; background:#eee;
    }
    .search-bar button {
      border-radius:0 20px 20px 0; border:1px solid #ff4b2b;
      background:#ff4b2b; color:#fff; font-weight:bold; padding:10px 20px;
      cursor:pointer; transition:background-color .2s;
    }
    .search-bar button:hover { background:#ff416c; }

    table {
      margin:20px auto; width:90%; border-radius:10px; overflow:hidden;
      box-shadow:0 4px 10px rgba(0,0,0,0.1); background:#fff;
    }
    th { background:#ff4b2b; color:#fff; }

    /* Botão Listar Todos estilizado como card */
    .btn-listar {
      display:inline-block; margin:20px auto;
      background:white; border-radius:12px; padding:20px 30px;
      box-shadow:0 2px 8px rgba(0,0,0,0.1); cursor:pointer;
      font-weight:bold; color:#ff4b2b; border:2px solid transparent;
      transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    }
    .btn-listar:hover {
      transform: translateY(-3px);
      box-shadow:0 6px 12px rgba(0,0,0,0.15);
      border-color: #ff416c;
      color: #ff416c;
    }
  </style>
</head>
<body>
  <header>
    <h1>📊 Dashboard de Usuários</h1>
    <p>Gerenciamento de cadastros</p>
  </header>

  <!-- Botão de Relatório PDF -->
  <div class="container mt-3 d-flex justify-content-end">
    <a href="#" id="btnRelatorio" class="btn btn-danger shadow-sm">
      <i class="fa-solid fa-file-pdf"></i> Gerar Relatório PDF
    </a>
  </div>

  <!-- Botão Listar Todos estilizado -->
  <div class="btn-listar" onclick="listarTodos()">
    <i class="fa-solid fa-list"></i> Listar Todos
  </div>

  <!-- Barra de Pesquisa -->
  <form method="GET" class="search-bar" action="">
    <input type="text" name="tipo" list="tipos" placeholder="Pesquisar por tipo ou ocupação" value="<?= htmlspecialchars($filtro) ?>">
    <datalist id="tipos">
      <option value="cliente">
      <option value="funcionario">
    </datalist>
    <button type="submit"><i class="fa fa-search"></i></button>
  </form>

  <!-- Tabela de Usuários -->
  <table id="tabelaUsuarios" class="table table-striped table-bordered" style="display: <?= $mostrarTabela ? 'table' : 'none' ?>;">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Ocupação</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($usuarios)): ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u["nome"]) ?></td>
                    <td><?= htmlspecialchars($u["email"]) ?></td>
                    <td><?= htmlspecialchars($u["tipo"]) ?></td>
                    <td><?= htmlspecialchars($u["ocupacao"]) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>


  <script>
    function listarTodos() {
      window.location.href = "?listar=1";
    }

    // Botão PDF com filtro dinâmico
    const btn = document.getElementById('btnRelatorio');
    btn.addEventListener('click', () => {
      const filtro = document.querySelector('input[name="tipo"]').value;
      let url = 'relatoriopdf.php?listar=1';
      if(filtro) url += '&tipo=' + encodeURIComponent(filtro);
      window.open(url, '_blank');
    });
  </script>
</body>
</html>
