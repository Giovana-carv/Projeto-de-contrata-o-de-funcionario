<?php
session_start();
include("../php/conexao.php");

// Redireciona se não for cliente logado
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_cliente = $_SESSION['id'];

// Foto do cliente para navbar
$stmtFoto = $conn->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
$stmtFoto->bind_param("i", $id_cliente);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = $resFoto->fetch_assoc();

// Cliente clicou em "apagar" a notificação
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_contratacao'])) {
    $id_contratacao = (int)$_POST['id_contratacao'];

    $verifica = $conn->prepare("SELECT id FROM contratacoes WHERE id = ? AND id_cliente = ?");
    $verifica->bind_param("ii", $id_contratacao, $id_cliente);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        $apagar = $conn->prepare("UPDATE contratacoes SET notificacao_cliente = '' WHERE id = ?");
        $apagar->bind_param("i", $id_contratacao);
        $apagar->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Consulta notificações ainda visíveis
$sql = "SELECT id, notificacao_cliente, data_solicitacao 
        FROM contratacoes 
        WHERE id_cliente = ? AND notificacao_cliente != ''
        ORDER BY data_solicitacao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Notificações do Cliente</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: Arial, sans-serif;
        background-color: #fffaf3;
        color: #212121;
    }
    .nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background-color: #ff416c;
        color: #fff;
        flex-wrap: wrap;
    }
    .nav .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: bold;
    }
    .nav .logo img {
        width: 50px; height: 50px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    .nav .links {
        display: flex;
        gap: 15px;
        font-weight: bold;
    }
    .nav .links a {
        color: white;
        font-size: 1.1rem;
        position: relative;
        text-decoration: none;
    }
    .nav .links a.login {
        background-color: #fff;
        color: black;
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
    }
    h2 {
        text-align: center;
        margin-top: 20px;
        color: black;
    }
    .notificacoes-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 1rem;
    max-width: 600px; /* largura mais estreita */
    margin: 20px auto; /* centraliza horizontalmente */
}

.card {
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px #ff416c;
    padding: 12px 16px; /* padding mais curto */
    text-align: center; /* centraliza texto e botões */
}

.card p {
    margin-bottom: 5px;
}

.card small {
    display: block;
    color: #666;
    margin-bottom: 10px;
}

.card form {
    display: flex;
    justify-content: center; /* botão no centro */
}

/* Botões (Apagar e Voltar) */
.btn {
    background-color: rgb(185, 40, 74);
    color: #fff;
    border: none;
    padding: 6px 14px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: bold;
    transition: background-color 0.3s, transform 0.2s;
}

.btn:hover {
    background-color: rgb(170, 36, 67);
    transform: scale(1.05);
}

/* Form de "Voltar" também centralizado */
form[method='get'] {
    display: flex;
    justify-content: center;
    margin-top: 15px;
}

</style>
</head>
<body>

<nav class="nav">
    <div class="logo">
        <?php if (!empty($foto['foto_perfil'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($foto['foto_perfil']); ?>" alt="Foto de perfil">
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($_SESSION['nome']); ?></h1>
    </div>
    <div class="links">
        <a href="../php/top10.php">Top 10</a>
        <a href="../php/editar_perfil.php">Editar Perfil</a>
        <a href="../php/pesquisar.php">Pesquisar</a>
        <a href="notificacoes.php">Notificações</a>
        <a class="login" href="../php/logout.php">Sair</a>
    </div>
</nav>

<h2>🔔 Respostas dos Pedidos</h2>

<div class="notificacoes-container">
<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
            <p><?= htmlspecialchars($row['notificacao_cliente']) ?></p>
            <small><?= date('d/m/Y H:i', strtotime($row['data_solicitacao'])) ?></small>
            <form method="post">
                <input type="hidden" name="id_contratacao" value="<?= $row['id'] ?>">
                <button type="submit" class="btn">Apagar</button>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;">Você não tem notificações.</p>
<?php endif; ?>

<form action='../cliente/painel_cliente.php' method='get' style="text-align:center;">
    <button type='submit' class="btn">Voltar</button>
</form>
</div>

</body>
</html>
