<?php
session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
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

// Buscar notificações pendentes
$sqlNotif = "SELECT u.nome AS nome_cliente, c.notificacao_funcionario, c.id, c.data_solicitacao
             FROM contratacoes c
             JOIN usuarios u ON c.id_cliente = u.id
             WHERE c.id_funcionario = ? AND c.status = 'pendente' AND c.notificacao_funcionario IS NOT NULL";

$stmtNotif = $conn->prepare($sqlNotif);
$stmtNotif->bind_param("i", $id_funcionario);
$stmtNotif->execute();
$resNotif = $stmtNotif->get_result();

$stmtNotif = $conn->prepare($sqlNotif);
$stmtNotif->bind_param("i", $id_func);
$stmtNotif->execute();
$resNotif = $stmtNotif->get_result();

// Buscar contratações finalizadas (histórico)
$sqlFinalizados = "SELECT u.nome
                   FROM contratacoes c
                   JOIN usuarios u ON c.id_cliente = u.id
                   WHERE c.id_funcionario = ? AND c.status = 'finalizado'";

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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
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
            color: white;
            font-weight: bold;
            font-size: 1rem;
            padding: 0.4rem 0.8rem;
            transition: 0.3s;
        }
        .nav .links a:not(.login):hover {
            color: #fff;
            border-bottom: 2px solid #fff;
        }
        .nav .links .login {
            background-color: #fff;
            color: #212121;
            padding: 0.5rem 1.5rem;
            border-radius: 1.5rem;
        }
        .nav .links .login:hover {
            background-color:rgb(201, 60, 35);
            color: #fff;
        }
        h2 {
            padding: 1rem;
            text-align: center;
        }
        .servico {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 10px;
            width: 300px;
            margin: 15px;
            max-width: 100%;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        .servico img {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .servicos-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        @media(max-width: 600px) {
            .nav {
                flex-direction: column;
                align-items: flex-start;
            }
            .nav .links {
                justify-content: flex-start;
                margin-top: 10px;
            }
            h2 {
                font-size: 1.3rem;
            }
        }
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
            <a href="../php/solicitacoes.php">Solicitações de Contratação</a>
            <a href="../php/em_atendimento.php">Em Atendimento</a>
            <a href="../php/editar_perfil.php">Editar Perfil</a>
            <a href="#">Sobre nós</a>
            <a class="login" href="../php/logout.php">Sair</a>
        </div>
    </nav>

    <h2>Histórico de Serviços</h2>

    <div class="servicos-container">
        <?php if ($resFinalizados && $resFinalizados->num_rows > 0): ?>
            <?php while($row = $resFinalizados->fetch_assoc()): ?>
                <div class="servico">
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome']) ?></p>
                    <?php if (!empty($row['fotos_servico'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['fotos_servico']) ?>" alt="Serviço">
                    <?php else: ?>
                        <p>Sem imagem disponível.</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">Nenhum serviço finalizado encontrado.</p>
        <?php endif; ?>
    </div>
    
</body>
</html>
