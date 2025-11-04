<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$cliente_id = $_SESSION['id'];

// Notificações do cliente
$stmt = $conn->prepare("SELECT c.id, c.status, c.notificacao_cliente, u.nome AS nome_funcionario
                        FROM contratacoes c
                        JOIN usuarios u ON c.id_funcionario = u.id
                        WHERE c.id_cliente = ? AND c.notificacao_cliente != ''");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$res = $stmt->get_result();

// Foto do cliente
$stmtFoto = $conn->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
$stmtFoto->bind_param("i", $cliente_id);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = $resFoto->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificações - Cliente</title>
<style>
    * {margin:0;padding:0;box-sizing:border-box;}
    body {font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif; background:#f9fafc; color:#333; line-height:1.6; transition:background 0.4s,color 0.4s;}
    header {background:linear-gradient(135deg,#ff416c,rgb(187, 7, 49)); padding:20px; text-align:center; box-shadow:0 4px 8px rgba(0,0,0,0.15); border-bottom-left-radius:15px; border-bottom-right-radius:15px; position:relative;}
    nav {display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;}
    nav .logo {display:flex; align-items:center; gap:15px; font-weight:bold; color:white;}
    nav .logo img {width:60px; height:60px; border-radius:50%; object-fit:cover; border:3px solid #fff; box-shadow:0 3px 6px rgba(0,0,0,0.2);}
    nav ul {display:flex; gap:20px; list-style:none; align-items:center;}
    nav li a, nav li button {text-decoration:none; color:#fff; font-weight:600; font-size:1rem; padding:10px 18px; border-radius:25px; transition:all 0.3s ease-in-out; border:none; background:none; cursor:pointer;}
    nav li a:hover, nav li a.active {background:#fff; color:#ff416c; box-shadow:0 3px 8px rgba(0,0,0,0.2);}
    .theme-toggle {background:rgba(255,255,255,0.2); border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center;}
    .theme-toggle:hover {background:rgba(255,255,255,0.35);}
    .menu-toggle {display:none; flex-direction:column; cursor:pointer; position:absolute; right:20px; top:25px; z-index:1000;}
    .menu-toggle span {height:3px;width:25px;background:#fff; margin:4px 0;border-radius:2px;transition:0.4s;}
    .menu-toggle.active span:nth-child(1){transform:rotate(45deg) translate(5px,5px);}
    .menu-toggle.active span:nth-child(2){opacity:0;}
    .menu-toggle.active span:nth-child(3){transform:rotate(-45deg) translate(6px,-6px);}
    main {padding:40px 20px; max-width:1200px; margin:auto; display:flex; flex-wrap:wrap; gap:20px;}
    .card {background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); text-align:center; transition:transform 0.3s,background 0.4s,color 0.4s; width:100%; max-width:500px; margin:auto; position:relative;}
    .card:hover {transform:translateY(-3px);}
    .delete-btn {position:absolute; top:10px; right:10px; background:#ff416c; color:#fff; border:none; padding:5px 10px; border-radius:8px; cursor:pointer; transition:background 0.3s;}
    .delete-btn:hover {background:rgb(187,7,49);}
    h2,h3 {margin-bottom:15px; text-align:center;}
    h2 {color:#ff416c;}
    h3 {color:rgb(187, 7, 49);}
    button {margin-top:8px;padding:8px 12px;border:none; border-radius:8px;background:#ff416c; color:#fff;cursor:pointer;transition:background 0.3s;}
    button:hover {background:rgb(187, 7, 49);}
    body.dark {background:#1e1e1e;color:#ddd;}
    body.dark .card {background:#2a2a2a;color:#ddd;}
    @media(max-width:768px){
        nav ul {flex-direction:column; position:absolute; top:100%; right:0; background:#ff416c; width:220px; display:none; padding:15px; border-bottom-left-radius:10px; box-shadow:0 5px 10px rgba(0,0,0,0.2);}
        nav ul.show {display:flex;}
        .menu-toggle {display:flex;}
    }
</style>
</head>
<body>
<header>
<nav>
    <div class="logo">
        <?php if (!empty($foto['foto_perfil'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($foto['foto_perfil']); ?>" alt="foto de perfil">
        <?php endif; ?>
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
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
<?php if ($res->num_rows > 0): ?>
    <?php while($n = $res->fetch_assoc()): ?>
        <div class="card">
            <button class="delete-btn">×</button>
            <h3><?php echo htmlspecialchars($n['nome_funcionario']); ?></h3>
            <p><?php echo htmlspecialchars($n['notificacao_cliente']); ?></p>
            <p>Status: <?php echo htmlspecialchars($n['status']); ?></p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Nenhuma notificação no momento.</p>
<?php endif; ?>
</main>

<script>
// Menu hambúrguer
const toggle = document.getElementById("menu-toggle");
const menu = document.getElementById("menu");
toggle.addEventListener("click", () => {
    menu.classList.toggle("show");
    toggle.classList.toggle("active");
});

// Tema claro/escuro
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

// Função para apagar notificação apenas na tela
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.target.parentElement.remove();
    });
});
</script>
</body>
</html>
