<?php
session_start();

// Permitir apenas funcionários
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
    $params[] = $id_funcionario;
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: ../funcionario/painel_funcionario.php");
        exit;
    }
}

// Buscar dados do funcionário
$stmt = $conn->prepare("SELECT nome, email, telefone, endereco, foto_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();

// Buscar foto do perfil para nav
$fotoPerfil = !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : 'default.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Perfil - Funcionário</title>
<style>
* {
    margin: 0; padding: 0; box-sizing: border-box;
}
body {
    font-family: Arial, sans-serif;
    background-color: #fff;
    color: #212121;
}
a {
    text-decoration: none;
    color: #fff;
}
.nav {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 1.5rem;
    color: #fff;
    background-color: #ff4b2b;
}
.nav .logo {
    display: flex;
    align-items: center;
    gap: 10px;
}
.nav .logo img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
}
.nav .links {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}
.nav .links a {
    font-weight: bold;
    color: white;
    padding: 0.4rem 0.8rem;
    transition: 0.3s;
}
.nav .links a:not(.login):hover {
    border-bottom: 2px solid #fff;
}
.nav .links .login {
    background-color: #fff;
    color: #212121;
    padding: 0.5rem 1.5rem;
    border-radius: 1.5rem;
}
.nav .links .login:hover {
    background-color: rgb(201,60,35);
    color: #fff;
}
h2 {
    text-align: center;
    margin: 1.5rem 0;
}
.container {
    max-width: 500px;
    margin: 2rem auto;
    background: #f9f9f9;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0,0,0,0.1);
}
.form-group {
    margin-bottom: 1.2rem;
}
label {
    display: block;
    font-weight: bold;
    margin-bottom: 0.4rem;
}
input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
    width: 100%;
    padding: 0.6rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.button-group {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 1rem;
}
button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    background-color: #ff4b2b;
    color: #fff;
    cursor: pointer;
    transition: 0.2s;
}
button:hover {
    background-color: #fa2904ff;
}
@media(max-width:600px) {
    .nav { flex-direction: column; align-items: flex-start; }
    .nav .links { justify-content: flex-start; margin-top:10px; }
}

/*Tema escuro*/
.theme-toggle {
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.theme-toggle:hover {
    background: rgba(255,255,255,0.35);
    cursor: pointer;
}
body.dark {
    background:#1e1e1e;
    color:white;
}
body.dark h2 {
    color:white;
}
body.dark .container {
    background:#2a2a2a;
    color:white;
}
</style>
</head>
<body>
<nav class="nav">
    <div class="logo">
        <img src="../uploads/<?php echo htmlspecialchars($fotoPerfil); ?>" alt="foto de perfil">
        <h1>Bem-vindo, <?php echo htmlspecialchars($nomeFuncionario); ?>!</h1>
    </div>
    <div class="links">
       <a href="../funcionario/painel_funcionario.php">Início</a>
        <a href="../funcionario/galeria.php">Galeria</a>
        <a href="../php/solicitacoes.php">Solicitações de Contratação</a>
        <a href="../php/em_atendimento.php">Em Atendimento</a>
        <a href="../php/editar_perfil_funcionario.php">Editar Perfil</a>
        <a href="../funcionario/mensagens.php">Mensagens</a>
        <a href="#">Sobre nós</a>
        <a class="login" href="../php/logout.php">Sair</a>
        <button class="theme-toggle" id="theme-toggle">🌙</button>
    </div>
</nav>

<div class="container">
    <h2>Atualizar Informações</h2>
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
            <a href="../funcionario/painel_funcionario.php"><button type="button">Voltar</button></a>
        </div>
    </form>
</div>
<script src="../js/tema.js"></script>
</body>
</html>
