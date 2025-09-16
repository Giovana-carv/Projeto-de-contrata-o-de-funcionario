<?php
session_start();
if ($_SESSION['tipo'] !== 'cliente' && $_SESSION['tipo'] !== 'funcionario') {
    echo "Acesso restrito.";
    exit;
}

include("../php/conexao.php");

$tipo = $_SESSION['tipo'];
$usuarios = ($tipo == 'cliente') ? 'cliente' : 'funcionario';

// Verifica login
if (!isset($_SESSION['id'])) {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$usuario_id = $_SESSION['id'];

// Pega o destinatário pelo GET
if (!isset($_GET['destinatario_id']) || !is_numeric($_GET['destinatario_id'])) {
    echo "Destinatário inválido.";
    exit;
}
$destinatario_id = (int)$_GET['destinatario_id'];

// Pega nome do destinatário
$destinatario = $conn->prepare("SELECT nome, foto_perfil FROM usuarios WHERE id = ?");
$destinatario->bind_param("i", $destinatario_id);
$destinatario->execute();
$res = $destinatario->get_result();

$dados_destinatario = $res->fetch_assoc();

if (!$dados_destinatario) {
    echo "Usuário não encontrado.";
    exit;
}

$nome_destinatario = $dados_destinatario['nome'];
$foto_destinatario = $dados_destinatario['foto_perfil'];

// Mensagens
$msg_stmt = $conn->prepare("SELECT * FROM mensagens WHERE 
    (remetente_id = ? AND destinatario_id = ?) OR 
    (remetente_id = ? AND destinatario_id = ?) ORDER BY data_envio ASC");
$msg_stmt->bind_param("iiii", $usuario_id, $destinatario_id, $destinatario_id, $usuario_id);
$msg_stmt->execute();
$msg_result = $msg_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat com <?php echo htmlspecialchars($nome_destinatario); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background-color: #fffaf3;
            color: #212121;
        }
        a { text-decoration: none; color: #fff; transition: color 0.2s ease; }

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
        .nav .links a:hover::before { width: 100%; }
        .nav .links a.login {
            background-color: #fff;
            color: black;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: bold;
        }
        .nav .links a.login:hover { background-color: rgb(187, 7, 49); color: #fff; }

        h3 { padding: 1rem; text-align: center; color: black; }

        .chat-container {
            display: flex;
            flex-direction: column;
            max-width: 700px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px #ff416c;
            padding: 20px;
        }

        .destinatario {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .destinatario img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ff416c;
        }

        .mensagem {
            margin: 10px 0;
            padding: 10px 15px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
        }
        .enviada { background-color: #d1ffd6; margin-left: auto; text-align: right; }
        .recebida { background-color: #e6e6e6; margin-right: auto; }

        form {
            margin-top: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        textarea {
            resize: none;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px;
            border: none;
            border-radius: 8px;
            background-color: rgb(185, 40, 74);
            color: #fff;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        button:hover { background-color: rgb(170, 36, 67); }

        .actions { display: flex; gap: 10px; flex-wrap: wrap; }
        @media(max-width: 600px) {
            .chat-container { padding: 15px; }
            .mensagem { max-width: 90%; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="logo">
            <?php if (!empty($_SESSION['foto_perfil'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($_SESSION['foto_perfil']); ?>" alt="Foto de perfil">
            <?php endif; ?>
            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
        </div>
        <div class="links">
            <?php if($tipo == 'cliente'): ?>
                <a href="../php/top10.php">Top 10 Funcionários</a>
                <a href="../php/editar_perfil.php">Editar Perfil</a>
                <a href="../php/pesquisar.php">Pesquisar</a>
                <a href="notificacoes.php">Notificações</a>
            <?php endif; ?>
            <a class="login" href="../php/logout.php">Sair</a>
        </div>
    </nav>

    <div class="chat-container">
        <div class="destinatario">
            <?php if (!empty($foto_destinatario)): ?>
                <img src="../uploads/<?php echo htmlspecialchars($foto_destinatario); ?>" alt="Foto de <?php echo htmlspecialchars($nome_destinatario); ?>">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($nome_destinatario); ?></h3>
        </div>

        <div class="mensagens">
            <?php while($m = $msg_result->fetch_assoc()): ?>
                <div class="mensagem <?php echo $m['remetente_id'] == $usuario_id ? 'enviada' : 'recebida'; ?>">
                    <?php echo nl2br(htmlspecialchars($m['mensagem'])); ?>
                    <br><small><?php echo date('d/m H:i', strtotime($m['data_envio'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <form action="enviar_mensagem.php" method="POST">
            <input type="hidden" name="destinatario_id" value="<?php echo $destinatario_id; ?>">
            <textarea name="mensagem" required placeholder="Digite sua mensagem..."></textarea>
            <div class="actions">
                <button type="submit">Enviar</button>
                <?php
                    $linkVoltar = ($tipo == 'cliente') ? '../cliente/painel_cliente.php' : '../funcionario/painel_funcionario.php';
                ?>
                <a href="<?php echo $linkVoltar; ?>"><button type="button">Voltar para o Painel</button></a>
            </div>
        </form>

        <?php if ($tipo === 'cliente'): ?>
            <form action="../php/contratar.php" method="POST">
                <input type="hidden" name="funcionario_id" value="<?= htmlspecialchars($destinatario_id) ?>">
                <button type="submit">Contratar</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
