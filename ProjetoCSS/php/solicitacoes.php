<?php 
session_start();
include("conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_funcionario = $_SESSION['id'];
$nomeFuncionario = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Funcionário';

// Buscar foto do perfil
$sqlFoto = "SELECT foto_perfil FROM usuarios WHERE id = ?";
$stmtFoto = $conn->prepare($sqlFoto);
$stmtFoto->bind_param("i", $id_funcionario);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = ($resFoto && $resFoto->num_rows > 0) ? $resFoto->fetch_assoc() : ['foto_perfil' => 'default.png'];

// Buscar solicitações pendentes
$sql = "SELECT c.*, u.nome AS nome_cliente, u.foto_perfil
        FROM contratacoes c
        JOIN usuarios u ON c.id_cliente = u.id
        WHERE c.id_funcionario = ? AND c.acao = 'pendente'
        ORDER BY c.data_solicitacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações Recebidas</title>
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

        /* ===== Navbar ===== */
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

        /* ====== Card Solicitação ====== */
        .pedido-container {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 10px;
            width: 350px;
            margin: 15px;
            max-width: 100%;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            text-align: center;
        }
        .pedido-container img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin: 10px 0;
            border: 3px solid #ff4b2b;
        }
        .solicitacoes-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        /* ===== Botões ===== */
        .botao-aceitar,
        .botao-recusar {
            border: none;
            padding: 10px 18px;
            font-size: 15px;
            border-radius: 8px;
            cursor: pointer;
            margin: 5px;
            display: inline-block;
            transition: background 0.3s ease, transform 0.2s ease;
            text-align: center;
        }
        .botao-aceitar {
            background: #28a745;
            color: #fff;
        }
        .botao-aceitar:hover {
            background: #218838;
            transform: scale(1.05);
        }
        .botao-recusar {
            background: #dc3545;
            color: #fff;
        }
        .botao-recusar:hover {
            background: #b02a37;
            transform: scale(1.05);
        }

        /* ===== Tema Escuro ===== */
        .theme-toggle {
            background:rgba(255,255,255,0.2);
            border-radius:50%;
            width:40px;
            height:40px;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .theme-toggle:hover {
            background:rgba(255,255,255,0.35);
            cursor: pointer;
        }
        body.dark {
            background:#1e1e1e;
            color: black;
        }
        body.dark h2 {
            color:white;
        }
        body.dark .pedido-container {
            background:#2a2a2a;
            color:white;
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
    <!-- Navbar igual ao painel principal -->
    <nav class="nav">
        <div class="logo">
            <img src="../uploads/<?php echo htmlspecialchars($foto['foto_perfil']); ?>" alt="foto de perfil">
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

    <h2>Solicitações Recebidas</h2>

    <div class="solicitacoes-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="pedido-container">
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>
                    <?php if (!empty($row['foto_perfil'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['foto_perfil']) ?>" alt="Foto de perfil">
                    <?php else: ?>
                        <p>Sem foto de perfil</p>
                    <?php endif; ?>
                    <?php if (isset($row['data_solicitacao'])): ?>
                        <p><strong>Data da solicitação:</strong> <?= date('d/m/Y H:i', strtotime($row['data_solicitacao'])) ?></p>
                    <?php endif; ?>

                    <a href="aceitar.php?id=<?= $row['id'] ?>" class="botao-aceitar">Aceitar</a>
                    <a href="../php/recusar.php?id=<?= $row['id'] ?>" class="botao-recusar">Recusar</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">Nenhuma solicitação recebida.</p>
        <?php endif; ?>
    </div>

    <script src="../js/tema.js"></script>
</body>
</html>
