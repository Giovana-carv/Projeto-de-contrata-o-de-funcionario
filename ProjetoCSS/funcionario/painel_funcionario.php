<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastrov.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$id_funcionario = intval($_SESSION['id']);
$nomeFuncionario = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Funcionário';

// Buscar foto do perfil
$sqlFoto = "SELECT foto_perfil FROM usuarios WHERE id = ?";
$stmtFoto = $conn->prepare($sqlFoto);
$stmtFoto->bind_param("i", $id_funcionario);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = ($resFoto && $resFoto->num_rows > 0) ? $resFoto->fetch_assoc() : ['foto_perfil' => 'default.png'];

// Buscar histórico de contratações finalizadas
$sqlFinalizados = "SELECT u.nome, c.foto_servico, c.data_finalizacao
                   FROM contratacoes c
                   JOIN usuarios u ON c.id_cliente = u.id
                   WHERE c.id_funcionario = ? AND c.status = 'finalizado'
                   ORDER BY c.data_finalizacao DESC";
$stmtHist = $conn->prepare($sqlFinalizados);
$stmtHist->bind_param("i", $id_funcionario);
$stmtHist->execute();
$resFinalizados = $stmtHist->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel do Funcionário</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:Arial,sans-serif;background:#f6f6f6}
    a{text-decoration:none;color:#fff}
    .nav{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;
         padding:0.8rem 1.5rem;color:#fff;background:#ff4b2b}
    .nav .logo{display:flex;align-items:center;gap:10px}
    .nav .logo img{width:50px;height:50px;object-fit:cover;border-radius:50%}
    .nav .links{display:flex;flex-wrap:wrap;gap:10px;margin-top:10px}
    .nav .links a{color:white;font-weight:bold;font-size:1rem;padding:0.4rem 0.8rem;transition:.3s}
    .nav .links a:not(.login):hover{border-bottom:2px solid #fff}
    .nav .links .login{background:#fff;color:#212121;padding:0.5rem 1.5rem;border-radius:1.5rem}
    .nav .links .login:hover{background:rgb(201,60,35);color:#fff}
    h2{text-align:center;margin:1.5rem 0;color:#333}

    /* Cards do histórico */
    .servicos-container{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
      gap:20px;
      padding:20px;
      max-width:1200px;
      margin:auto;
    }
    .servico{
      background:#fff;
      border-radius:12px;
      overflow:hidden;
      box-shadow:0 4px 10px rgba(0,0,0,0.1);
      transition:transform .2s;
    }
    .servico:hover{transform:translateY(-5px)}
    .servico img {
  width: 100%;
  max-height: 200px;   /* limita a altura */
  object-fit: contain; /* mostra a imagem inteira */
  display: block;
  border-bottom: 1px solid #ddd;
  background: #f9f9f9; /* fundo neutro caso a imagem não ocupe tudo */
}

    .servico .info{
      padding:15px;
    }
    .servico .info h3{
      font-size:1.1rem;
      margin-bottom:8px;
      color:#333;
    }
    .servico .info p{
      font-size:0.9rem;
      color:#555;
      margin-bottom:5px;
    }
    .no-data{
      text-align:center;
      margin:40px;
      font-size:1.1rem;
      color:#777;
    }

    /* Tema escuro */
    body.dark{background:#1e1e1e;color:#f0f0f0}
    body.dark h2{color:white}
    body.dark .servico{background:#2a2a2a;color:white}
    body.dark .servico .info h3{color:#fff}
    body.dark .servico .info p{color:#ccc}
    .theme-toggle{background:rgba(255,255,255,0.2);border-radius:50%;
      width:40px;height:40px;display:flex;align-items:center;justify-content:center}
    .theme-toggle:hover{background:rgba(255,255,255,0.35);cursor:pointer}
  </style>
</head>
<body>
  <nav class="nav">
    <div class="logo">
      <img src="../uploads/<?php echo htmlspecialchars($foto['foto_perfil']); ?>" alt="foto de perfil">
      <h1>Bem-vindo, <?php echo htmlspecialchars($nomeFuncionario); ?>!</h1>
    </div>
    <div class="links">
      <a href="galeria.php">Galeria</a>
      <a href="../php/solicitacoes.php">Solicitações</a>
      <a href="../php/em_atendimento.php">Em Atendimento</a>
      <a href="../php/editar_perfil_funcionario.php">Editar Perfil</a>
      <a href="mensagens.php">Mensagens</a>
      <a href="#">Sobre nós</a>
      <a class="login" href="../php/logout.php">Sair</a>
      <button class="theme-toggle" id="theme-toggle">🌙</button>
    </div>
  </nav>

  <h2>Histórico de Serviços</h2>

  <div class="servicos-container">
    <?php if ($resFinalizados && $resFinalizados->num_rows > 0): ?>
      <?php while($row = $resFinalizados->fetch_assoc()): ?>
        <div class="servico">
          <?php if (!empty($row['foto_servico'])): ?>
            <img src="../uploads/<?= htmlspecialchars($row['foto_servico']) ?>" alt="Serviço">
          <?php else: ?>
            <img src="../uploads/default.png" alt="Sem imagem">
          <?php endif; ?>
          <div class="info">
            <h3>Cliente: <?= htmlspecialchars($row['nome']) ?></h3>
            <p><strong>Finalizado em:</strong> <?= date("d/m/Y", strtotime($row['data_finalizacao'])) ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-data">Nenhum serviço finalizado encontrado.</p>
    <?php endif; ?>
  </div>

  <script src="../js/tema.js"></script>
</body>
</html>
