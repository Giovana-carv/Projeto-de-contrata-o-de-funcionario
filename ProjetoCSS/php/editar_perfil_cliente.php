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
$nomeCliente = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Cliente';

// Foto do cliente
$stmtFoto = $conn->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
$stmtFoto->bind_param("i", $cliente_id);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = $resFoto->fetch_assoc();

// Atualizar dados do perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!empty($_FILES['foto_perfil']['name'])) {
        $foto_nome = basename($_FILES['foto_perfil']['name']);
        $foto_destino = "../uploads/" . $foto_nome;
        move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_destino);
    }

    $query = "UPDATE usuarios SET nome=?, email=?, telefone=?, endereco=?";
    $params = [$nome, $email, $telefone, $endereco];
    $types = "ssss";

    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $query .= ", senha=?";
        $params[] = $senha_hash;
        $types .= "s";
    }

    if (!empty($_FILES['foto_perfil']['name'])) {
        $query .= ", foto_perfil=?";
        $params[] = $foto_nome;
        $types .= "s";
    }

    $query .= " WHERE id=?";
    $params[] = $cliente_id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: ../cliente/painel_cliente.php");
        exit;
    }
}

// Buscar dados do cliente
$stmt = $conn->prepare("SELECT nome, email, telefone, endereco, foto_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();
$fotoPerfil = !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : 'default.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Perfil - Cliente</title>
<style>
* {margin:0;padding:0;box-sizing:border-box;}
body {
    font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;
    background:#f9fafc;
    color:#333;
    line-height:1.6;
    transition:background 0.4s,color 0.4s;
}
header {
    background:linear-gradient(135deg,#ff416c,rgb(187,7,49));
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
    width:60px;
    height:60px;
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
    width:40px;
    height:40px;
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
    right:20px;
    top:25px;
    z-index:1000;
}
.menu-toggle span {
    height:3px;width:25px;background:#fff;
    margin:4px 0;border-radius:2px;transition:0.4s;
}
.menu-toggle.active span:nth-child(1){transform:rotate(45deg) translate(5px,5px);}
.menu-toggle.active span:nth-child(2){opacity:0;}
.menu-toggle.active span:nth-child(3){transform:rotate(-45deg) translate(6px,-6px);}
.container {
    max-width:500px;
    margin:40px auto;
    background:#fff;
    padding:2rem;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.container h2 {
    text-align:center;
    margin-bottom:1.5rem;
    color:rgb(187,7,49);
    font-size:1.6rem;
}
.form-group {margin-bottom:1.2rem;}
label {display:block;font-weight:bold;margin-bottom:0.4rem;font-size:1rem;}
input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
    width:100%;
    padding:0.6rem;
    border:1px solid #ccc;
    border-radius:5px;
    font-size:1rem;
}
.button-group {display:flex;justify-content:space-between;gap:10px;margin-top:1rem;}
button {padding:10px 15px;border:none;border-radius:5px;background:#ff416c;color:#fff;cursor:pointer;font-size:1rem;transition:0.2s;}
button:hover {background:rgb(187,7,49);}
body.dark {background:#1e1e1e;color:#ddd;}
body.dark .container {background:#2a2a2a;color:#ddd;}
@media(max-width:768px){
    nav ul {
        flex-direction:column;
        position:absolute;
        top:100%;
        right:0;
        background:#ff416c;
        width:220px;
        display:none;
        padding:15px;
        border-bottom-left-radius:10px;
        box-shadow:0 5px 10px rgba(0,0,0,0.2);
    }
    nav ul.show {display:flex;}
    .menu-toggle {display:flex;}
}
</style>
</head>
<body>
<header>
    <nav>
        <div class="logo">
            <img src="../uploads/<?php echo htmlspecialchars($fotoPerfil); ?>" alt="foto de perfil">
            <h1>Bem-vindo, <?php echo htmlspecialchars($nomeCliente); ?>!</h1>
        </div>
        <div class="menu-toggle" id="menu-toggle"><span></span><span></span><span></span></div>
        <ul id="menu">
            <li><a href="../php/top10.php">Top 10 Funcionários</a></li>
            <li><a href="../php/editar_perfil_cliente.php" class="active">Editar Perfil</a></li>
            <li><a href="../php/pesquisar.php">Pesquisar</a></li>
            <li><a href="notificacoes.php">Notificações</a></li>
            <li><a href="../php/logout.php">Sair</a></li>
            <li><button class="theme-toggle" id="theme-toggle">🌙</button></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h2>Editar Perfil</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" id="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>">
        </div>
        <div class="form-group">
            <label for="endereco">Endereço:</label>
            <input type="text" name="endereco" id="endereco" value="<?php echo htmlspecialchars($usuario['endereco']); ?>">
        </div>
        <div class="form-group">
            <label for="senha">Nova Senha:</label>
            <input type="password" name="senha" id="senha" placeholder="Deixe em branco para não alterar">
        </div>
        <div class="form-group">
            <label for="foto_perfil">Foto de Perfil:</label>
            <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">
        </div>
        <div class="button-group">
            <button type="submit">Salvar Alterações</button>
            <a href="../cliente/painel_cliente.php"><button type="button">Voltar</button></a>
        </div>
    </form>
</div>

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
</script>
</body>
</html>
