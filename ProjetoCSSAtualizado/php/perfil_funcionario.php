<?php
include('conexao.php');
session_start();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_funcionario = (int) $_GET['id'];
} else {
    echo "Funcionário inválido.";
    exit;
}

$id_cliente = $_SESSION['id'];

// Consulta dos serviços com fotos
$sql_fotos = "SELECT u.nome AS nome_funcionario, u.foto_perfil, c.foto_servico, c.data_solicitacao
    FROM contratacoes c
    JOIN usuarios u ON u.id = c.id_funcionario
    WHERE c.id_funcionario = ?
    AND c.foto_servico != ''
    ORDER BY c.data_solicitacao DESC";

$stmt = $conn->prepare($sql_fotos);
if (!$stmt) {
    die("Erro na consulta: " . $conn->error);
}

$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil do Funcionário</title>
  <style>
    * {margin:0;padding:0;box-sizing:border-box;}
    body {
      font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;
      background:#f9fafc;
      color:#333;
      transition:background 0.4s,color 0.4s;
    }
    header {
      background:linear-gradient(135deg,#ff416c,rgb(187, 7, 49));
      padding:20px;
      text-align:center;
      box-shadow:0 4px 8px rgba(0,0,0,0.15);
      border-bottom-left-radius:15px;
      border-bottom-right-radius:15px;
      position:relative;
    }
    nav {
      display:flex;
      align-items:center;
      justify-content:space-between;
      flex-wrap:wrap;
      gap:20px;
    }
    nav .logo {
      display:flex;
      align-items:center;
      gap:15px;
      font-weight:bold;
      color:white;
    }
    nav .logo img {
      width:60px;height:60px;
      border-radius:50%;
      object-fit:cover;
      border:3px solid #fff;
      box-shadow:0 3px 6px rgba(0,0,0,0.2);
    }
    nav ul {
      display:flex;
      gap:20px;
      list-style:none;
      align-items:center;
    }
    nav li a, nav li button {
      text-decoration:none;
      color:#fff;
      font-weight:600;
      font-size:1rem;
      padding:10px 18px;
      border-radius:25px;
      transition:all 0.3s ease-in-out;
      border:none;
      background:none;
      cursor:pointer;
    }
    nav li a:hover, nav li a.active {
      background:#fff;
      color:#ff416c;
      box-shadow:0 3px 8px rgba(0,0,0,0.2);
    }
    .theme-toggle {
      background:rgba(255,255,255,0.2);
      border-radius:50%;
      width:40px;height:40px;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .theme-toggle:hover {background:rgba(255,255,255,0.35);}
    .menu-toggle {
      display:none;
      flex-direction:column;
      cursor:pointer;
      position:absolute;
      right:20px;top:25px;
      z-index:1000;
    }
    .menu-toggle span {
      height:3px;width:25px;background:#fff;
      margin:4px 0;border-radius:2px;transition:0.4s;
    }
    .menu-toggle.active span:nth-child(1){transform:rotate(45deg) translate(5px,5px);}
    .menu-toggle.active span:nth-child(2){opacity:0;}
    .menu-toggle.active span:nth-child(3){transform:rotate(-45deg) translate(6px,-6px);}
    main {padding:40px 20px;max-width:1200px;margin:auto;}
    h2 {color:#ff416c;text-align:center;margin-bottom:20px;}
    .cards-container {
      display:flex;flex-wrap:wrap;gap:20px;justify-content:center;
    }
    .card {
      background:#fff;
      padding:20px;
      border-radius:12px;
      box-shadow:0 4px 12px rgba(0,0,0,0.08);
      text-align:center;
      transition:transform 0.3s,background 0.4s,color 0.4s;
      width:280px;
    }
    .card:hover {transform:translateY(-3px);}
    .card img {
      width:100%;max-height:220px;object-fit:cover;
      border-radius:8px;margin-bottom:10px;
      border:2px solid #ddd;
    }
    .card strong {color:rgb(187, 7, 49);}
    .btn {
      display:inline-block;
      margin-top:10px;
      padding:8px 14px;
      border:none;
      border-radius:8px;
      background:#ff416c;
      color:#fff;
      cursor:pointer;
      text-decoration:none;
      transition:background 0.3s;
    }
    .btn:hover {background:rgb(187, 7, 49);}
    body.dark {background:#1e1e1e;color:#ddd;}
    body.dark .card {background:#2a2a2a;color:#ddd;}
    @media(max-width:768px){
      nav ul {
        flex-direction:column;
        position:absolute;
        top:100%;right:0;
        background:#ff416c;
        width:220px;display:none;
        padding:15px;
        border-bottom-left-radius:10px;
        box-shadow:0 5px 10px rgba(0,0,0,0.2);
      }
      nav ul.show {display:flex;}
      .menu-toggle {display:flex;}
    }

    .btn-container {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 30px;
}

  </style>
</head>
<body>
<header>
  <nav>
    <div class="logo">
      <?php if ($result->num_rows > 0): 
        $primeiro = $result->fetch_assoc(); ?>
        <img src="../uploads/<?php echo htmlspecialchars($primeiro['foto_perfil']); ?>" alt="Foto do Funcionário">
        <h1><?php echo htmlspecialchars($primeiro['nome_funcionario']); ?></h1>
      <?php endif; ?>
    </div>
    <div class="menu-toggle" id="menu-toggle"><span></span><span></span><span></span></div>
    <ul id="menu">
       <li><a href="../cliente/painel_cliente.php">Início</a></li>
        <li><a href="../php/top10.php">Top 10 Funcionários</a></li>
        <li><a href="../php/editar_perfil_cliente.php">Editar Perfil</a></li>
        <li><a href="../php/pesquisar.php">Pesquisar</a></li>
        <li><a class="active" href="notificacoes.php">Notificações</a></li>
        <li><a href="../php/logout.php">Sair</a></li>
        <li><button class="theme-toggle" id="theme-toggle">🌙</button></li>
    </ul>
  </nav>
</header>

<main>
  <h2>Serviços Finalizados</h2>
  <div class="cards-container">
    <?php if (isset($primeiro)): ?>
      <div class="card">
        <img src="../uploads/<?php echo htmlspecialchars($primeiro['foto_servico']); ?>" alt="Foto serviço">
        <strong>Data:</strong> <?php echo htmlspecialchars($primeiro['data_solicitacao']); ?>
      </div>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="card">
        <img src="../uploads/<?php echo htmlspecialchars($row['foto_servico']); ?>" alt="Foto serviço">
        <strong>Data:</strong> <?php echo htmlspecialchars($row['data_solicitacao']); ?>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- Botões centralizados -->
  <div class="btn-container">
    <?php if ($_SESSION['id'] != $id_funcionario): ?>
      <a class="btn" href="../chat/chat.php?destinatario_id=<?= $id_funcionario ?>">Conversar</a>
    <?php endif; ?>
    <a class="btn" href='top10.php'>Voltar ao painel</a>
  </div>
</main>


<script>
  const toggle = document.getElementById("menu-toggle");
  const menu = document.getElementById("menu");
  toggle.addEventListener("click", () => {
    menu.classList.toggle("show");
    toggle.classList.toggle("active");
  });

  const themeToggle = document.getElementById("theme-toggle");
  const body = document.body;
  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
    themeToggle.textContent = "☀️";
  }
  themeToggle.addEventListener("click", () => {
    body.classList.toggle("dark");
    if (body.classList.contains("dark")) {
      themeToggle.textContent = "☀️";
      localStorage.setItem("theme", "dark");
    } else {
      themeToggle.textContent = "🌙";
      localStorage.setItem("theme", "light");
    }
  });
</script>
</body>
</html>
