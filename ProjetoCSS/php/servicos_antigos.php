<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
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

// Buscar serviços finalizados
$sqlFinalizados = "SELECT c.*, u.nome AS nome_cliente
                   FROM contratacoes c
                   JOIN usuarios u ON c.id_cliente = u.id
                   WHERE c.id_funcionario = ? AND c.status = 'finalizado'
                   ORDER BY c.data_solicitacao DESC";
$stmt = $conn->prepare($sqlFinalizados);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$resFinalizados = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Serviços Finalizados</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; }

    a { text-decoration:none; color:#fff; }
    h2 { text-align:center; padding:1rem; }

    /* Navbar */
    .nav {
        display:flex;
        flex-wrap:wrap;
        justify-content:space-between;
        align-items:center;
        padding:0.8rem 1.5rem;
        color:#fff;
        background-color:#ff4b2b;
    }
    .nav .logo { display:flex; align-items:center; gap:10px; }
    .nav .logo img { width:50px; height:50px; object-fit:cover; border-radius:50%; }
    .nav .links { display:flex; flex-wrap:wrap; justify-content:center; gap:10px; margin-top:10px; }
    .nav .links a { color:white; font-weight:bold; font-size:1rem; padding:0.4rem 0.8rem; transition:0.3s; }
    .nav .links a:not(.login):hover { color:#fff; border-bottom:2px solid #fff; }
    .nav .links .login { background-color:#fff; color:#212121; padding:0.5rem 1.5rem; border-radius:1.5rem; }
    .nav .links .login:hover { background-color:rgb(201,60,35); color:#fff; }

    /* Cards */
    .servicos-container { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; }
    .servico {
        border:1px solid #ccc;
        padding:15px;
        border-radius:10px;
        width:300px;
        max-width:100%;
        box-shadow:0 0 8px rgba(0,0,0,0.1);
        background-color:#f9f9f9;
    }
    .servico img {
        width:100%;
        height:auto;
        max-height:200px;
        object-fit:cover;
        border-radius:10px;
        margin-top:10px;
    }

    @media(max-width:600px) {
        .nav { flex-direction:column; align-items:flex-start; }
        .nav .links { justify-content:flex-start; margin-top:10px; }
        h2 { font-size:1.3rem; }
    }

    /* Tema escuro */
    .theme-toggle {
        background:rgba(255,255,255,0.2);
        border-radius:50%;
        width:40px; height:40px;
        display:flex; align-items:center; justify-content:center;
        cursor:pointer;
    }
    .theme-toggle:hover { background:rgba(255,255,255,0.35); }
    body.dark { background:#1e1e1e; color:white; }
    body.dark .servico { background:#2a2a2a; color:white; }
    body.dark h2 { color:white; }
</style>
</head>
<body>

<nav class="nav">
    <div class="logo">
        <img src="../uploads/<?php echo htmlspecialchars($foto['foto_perfil']); ?>" alt="foto de perfil">
        <h1>Bem-vindo, <?php echo htmlspecialchars($nomeFuncionario); ?>!</h1>
    </div>
    <div class="links">
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

<h2>Serviços Finalizados</h2>

<div class="servicos-container">
    <?php if ($resFinalizados && $resFinalizados->num_rows > 0): ?>
        <?php while($row = $resFinalizados->fetch_assoc()): ?>
            <div class="servico">
                <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>
                <?php if (!empty($row['foto_servico'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($row['foto_servico']) ?>" alt="Serviço finalizado">
                <?php else: ?>
                    <p>Sem imagem disponível</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">Nenhum serviço finalizado encontrado.</p>
    <?php endif; ?>
</div>

<script src="../js/tema.js"></script>
</body>
</html>
