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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Foto
    if (!empty($_FILES['foto_perfil']['name'])) {
        $foto_nome = basename($_FILES['foto_perfil']['name']);
        $foto_destino = "../uploads/" . $foto_nome;
        move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_destino);
    }

    // Monta query dinamicamente
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
        header("Location: ../php/painel_cliente.php");
        exit;
    }
}

// Busca dados do usuário
$stmt = $conn->prepare("SELECT nome, email, telefone, endereco, foto_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Perfil</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
/* Reset e base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: Arial, sans-serif;
    background-color: #fffaf3;
    color: #212121;
}
a {
    text-decoration: none;
    color: #fff;
    transition: color 0.2s ease;
}

/* Navbar */
.nav {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background-color: #ff416c;
    color: #fff;
}
.nav .logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: bold;
}
.nav .logo img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #fff;
}
.nav .links {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-weight: bold;
}
.nav .links a {
    font-size: 1.1rem;
    position: relative;
    color: white;
}
.nav .links a:not(.login)::before {
    content: "";
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 0;
    height: 2px;
    background-color: #fff;
    transition: width 0.2s ease;
}
.nav .links a:hover::before {
    width: 100%;
}
.nav .links a.login {
    background-color: #fff;
    color: black;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
}
.nav .links a.login:hover {
    background-color: rgb(187, 7, 49);
    color: #fff;
}

/* Container */
.container {
    max-width: 500px;
    margin: 2rem auto;
    background: #ffffff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 12px #ff416c;
}
.container h2 {
    text-align: center;
    margin-bottom: 1.5rem;
}

/* Formulário */
.form-group {
    margin-bottom: 1.2rem;
}
label {
    display: block;
    font-weight: bold;
    margin-bottom: 0.4rem;
}
input[type="text"],
input[type="email"],
input[type="password"],
input[type="file"] {
    width: 100%;
    padding: 0.6rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* Botões */
button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    background-color: rgb(185, 40, 74);
    color: #fff;
    cursor: pointer;
    transition: background-color 0.2s ease;
}
button:hover {
    background-color: rgb(170, 36, 67);
}
.button-group {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 1rem;
}
</style>
</head>
<body>
<nav class="nav">
    <div class="logo">
        <?php if (!empty($usuario['foto_perfil'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="foto de perfil">
        <?php endif; ?>
        <h1>Editar Perfil</h1>
    </div>
    <div class="links">
        <a href="../php/top10.php">Top 10 Funcionários</a>
        <a href="../php/editar_perfil.php">Editar Perfil</a>
        <a href="../php/pesquisar.php">Pesquisar</a>
        <a href="notificacoes.php">Notificações</a>
        <a class="login" href="../php/logout.php">Sair</a>
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
            <a href="../cliente/painel_cliente.php"><button type="button">Voltar</button></a>
        </div>
    </form>
</div>
</body>
</html>
