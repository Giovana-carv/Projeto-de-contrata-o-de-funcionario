<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastrov.html");
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

// Buscar serviços em atendimento
$sql = "SELECT c.*, u.nome AS nome_cliente, u.foto_perfil
        FROM contratacoes c
        JOIN usuarios u ON c.id_cliente = u.id
        WHERE c.id_funcionario = ? AND c.status = 'em atendimento' AND c.acao = 'aceito'
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
    <title>Serviços em Atendimento</title>
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

        /* ===== Card de Atendimento ===== */
        .card {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 10px;
            width: 350px;
            margin: 15px;
            max-width: 100%;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        .cliente-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        .cliente-info img {
            border-radius: 50%;
            border: 2px solid #ff4b2b;
            width: 70px;
            height: 70px;
            object-fit: cover;
        }
        .cliente-nome {
            font-size: 18px;
            font-weight: bold;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        input[type="file"] {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            margin-bottom: 10px;
        }
        .btn-finalizar {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        .btn-finalizar:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .servicos-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
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
        body.dark .card {
            background:#2a2a2a;
            color:white;
        }
        body.dark input[type="file"] {
            background:#3a3a3a;
            color:white;
            border: 1px solid #666;
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
            
            <a href="galeria.php">Galeria</a>
            <a href="../php/solicitacoes.php">Solicitações de Contratação</a>
            <a href="../php/em_atendimento.php">Em Atendimento</a>
            <a href="../php/editar_perfil_funcionario.php">Editar Perfil</a>
            <a href="../funcionario/mensagens.php">Mensagens</a>
            <a href="#">Sobre nós</a>
            <a class="login" href="../php/logout.php">Sair</a>
            <button class="theme-toggle" id="theme-toggle">🌙</button>
        </div>
    </nav>

    <h2>Serviços em Atendimento</h2>

    <div class="servicos-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="cliente-info">
                        <?php if (!empty($row['foto_perfil'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['foto_perfil']) ?>" alt="Foto do Cliente">
                        <?php else: ?>
                            <img src="../uploads/default.png" alt="Sem foto">
                        <?php endif; ?>
                        <div class="cliente-nome"><?= htmlspecialchars($row['nome_cliente']) ?></div>
                    </div>

                    <form action="../php/finalizar_servico.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_contratacao" value="<?= $row['id'] ?>">
                        <label for="foto_servico">Foto do serviço finalizado:</label>
                        <input type="file" name="foto_servico" id="foto_servico" required>
                        <button type="submit" class="btn-finalizar">Finalizar Serviço</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">Nenhum serviço em atendimento.</p>
        <?php endif; ?>
    </div>

    <script src="../js/tema.js"></script>
</body>
</html>
