<?php 
session_start();
include("../php/conexao.php"); // mantém seu include padrão (se preferir, pode trocar por new mysqli como no painel principal)

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$conn = isset($conn) ? $conn : new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$id_funcionario = intval($_SESSION['id']);
$nomeFuncionario = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Funcionário';

// Buscar foto do perfil (mesmo padrão do painel principal)
$sqlFoto = "SELECT foto_perfil FROM usuarios WHERE id = ?";
$stmtFoto = $conn->prepare($sqlFoto);
$stmtFoto->bind_param("i", $id_funcionario);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = ($resFoto && $resFoto->num_rows > 0) ? $resFoto->fetch_assoc() : ['foto_perfil' => 'default.png'];

// Buscar usuários (clientes) com quem houve troca de mensagens
$sql = "SELECT DISTINCT u.id, u.nome, u.foto_perfil 
        FROM mensagens m 
        JOIN usuarios u ON ((m.remetente_id = u.id OR m.destinatario_id = u.id) AND u.tipo = 'cliente') 
        WHERE (m.remetente_id = ? OR m.destinatario_id = ?) AND u.id != ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $id_funcionario, $id_funcionario, $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Notificações / Mensagens</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f6f5f7; color: #333; }

        a { text-decoration: none; color: inherit; }

        /* NAV igual ao painel principal */
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
            align-items: center;
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
            background-color: rgb(201, 60, 35);
            color: #fff;
        }

        h2 { padding: 1rem; text-align: center; }

        main { max-width: 900px; margin: 0 auto 40px; padding: 0 16px; }

        .info {
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 15px 18px;
            margin: 12px auto;
            display: flex;
            align-items: center;
            gap: 16px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            max-width: 720px;
            transition: transform 0.18s ease;
        }
        .info:hover { transform: translateX(4px); }

        .cliente {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
        }
        .cliente img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ff4b2b;
        }
        .cliente a {
            font-size: 18px;
            font-weight: 700;
            color: #212121;
        }
        .cliente a:hover { text-decoration: underline; color: #000; }

        .btn-voltar {
            display: inline-block;
            background-color: #ff4b2b;
            color: white;
            padding: 10px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            margin: 18px auto 0;
        }
        .btn-voltar:hover { opacity: 0.95; transform: translateY(-2px); }

        /* Tema escuro (mesmo padrão do painel) */
        .theme-toggle {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display:flex;
            align-items:center;
            justify-content:center;
            border: none;
            cursor: pointer;
        }
        .theme-toggle:hover { background: rgba(255,255,255,0.35); }

        body.dark {
            background: #1e1e1e;
            color: #f0f0f0;
        }
        body.dark h2 { color: #fff; }
        body.dark .info { background: #2a2a2a; box-shadow: 0 4px 12px rgba(0,0,0,0.5); }
        body.dark .cliente a { color: #f0f0f0; }
        body.dark .nav { background-color: #222; }
        body.dark .nav .links .login { background-color: #fff; color: #212121; }

        @media(max-width: 600px) {
            .nav { flex-direction: column; align-items: flex-start; }
            .nav .links { justify-content: flex-start; margin-top: 10px; }
            h2 { font-size: 1.2rem; }
            .cliente img { width: 60px; height: 60px; }
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
           <a href="../funcionario/painel_funcionario.php">Início</a>
            <a href="galeria.php">Galeria</a>
            <a href="../php/solicitacoes.php">Solicitações de Contratação</a>
            <a href="../php/em_atendimento.php">Em Atendimento</a>
            <a href="../php/editar_perfil_funcionario.php">Editar Perfil</a>
            <a href="mensagens.php">Mensagens</a>
            <a href="#">Sobre nós</a>
            <a class="login" href="../php/logout.php">Sair</a>
            <button class="theme-toggle" id="theme-toggle">🌙</button>
        </div>
    </nav>

    <main>
        <h2>Notificações</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($cliente = $result->fetch_assoc()): ?>
                <div class="info">
                    <div class="cliente">
                        <?php if (!empty($cliente['foto_perfil'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($cliente['foto_perfil']); ?>" alt="foto de perfil">
                        <?php else: ?>
                            <img src="../imagens/perfil_padrao.png" alt="sem foto">
                        <?php endif; ?>

                        <a href="../chat/chat.php?destinatario_id=<?php echo $cliente['id']; ?>">
                            <?php echo htmlspecialchars($cliente['nome']); ?>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; margin-top:16px;">Nenhuma notificação encontrada.</p>
        <?php endif; ?>

        <div style="text-align:center;">
            <a href="../funcionario/painel_funcionario.php" class="btn-voltar">⬅ Voltar</a>
        </div>
    </main>

    <script src="../js/tema.js"></script>
</body>
</html>
