<?php
session_start();
include("../php/conexao.php");

// Verifica se o funcionário está logado
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

// Garante conexão caso include não tenha definido $conn
if (!isset($conn) || !($conn instanceof mysqli)) {
    $conn = new mysqli("localhost", "root", "", "sistema_usuarios");
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }
}

$id_funcionario = intval($_SESSION['id']);
$nomeFuncionario = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Funcionário';

// Buscar foto do perfil (padronizado)
$sqlFoto = "SELECT foto_perfil FROM usuarios WHERE id = ?";
$stmtFoto = $conn->prepare($sqlFoto);
$stmtFoto->bind_param("i", $id_funcionario);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = ($resFoto && $resFoto->num_rows > 0) ? $resFoto->fetch_assoc() : ['foto_perfil' => 'default.png'];

// Consulta os serviços finalizados
$sql = "SELECT c.*, u.nome AS nome_cliente
        FROM contratacoes c
        JOIN usuarios u ON c.id_cliente = u.id
        WHERE c.id_funcionario = ? AND c.status = 'finalizado'
        ORDER BY c.data_solicitacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();

// Inserção de comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'], $_POST['id_contratacao'])) {
    $comentario = trim($_POST['comentario']);
    $id_contratacao = (int) $_POST['id_contratacao'];

    if ($comentario !== '') {
        $sqlComentario = "INSERT INTO comentarios_servico (id_contratacao, id_funcionario, comentario) VALUES (?, ?, ?)";
        $stmtComentario = $conn->prepare($sqlComentario);
        $stmtComentario->bind_param("iis", $id_contratacao, $id_funcionario, $comentario);
        $stmtComentario->execute();

        // Evitar reenvio do formulário ao atualizar a página
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Galeria de Serviços Finalizados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Reset e base */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f6f5f7; color: #333; }

        a { text-decoration: none; color: inherit; }

        /* NAV (padronizada com Painel do Funcionário) */
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

        /* Tema toggle dentro da nav (mesmo padrão) */
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

        main {
  max-width: 920px;
  margin: 20px auto 60px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  align-items: center; /* <--- centraliza horizontalmente */
}

        :root {
            --cor-primaria: #ff4b2b;
            --cor-primaria-hover: #e04326;
            --cor-clara: #fff5f0;
            --sombra-suave: rgba(0, 0, 0, 0.1);
            --texto-padrao: #333;
            --texto-suave: #666;
        }

        /* Cards de itens (manter estilo original, mas ajustado para responsividade) */
        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .item {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 10px var(--sombra-suave);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }
        .item img {
            width: 100%;
            border-radius: 10px;
            border: 2px solid var(--cor-primaria);
            max-height: 320px;
            object-fit: cover;
        }
        p { font-size: 14px; color: var(--texto-suave); margin-top: 10px; }
        .item p strong { color: var(--texto-padrao); }

        .botao-voltar {
            display: block;
            width: fit-content;
            margin: 16px auto;
            background-color: var(--cor-primaria);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            text-decoration: none;
            transition: background 0.3s, transform 0.15s;
        }
        .botao-voltar:hover { background-color: var(--cor-primaria-hover); transform: translateY(-2px); }

        .mensagem-vazia { text-align: center; font-size: 16px; color: var(--texto-suave); margin-top: 40px; }

        /* Comentários */
        .comentario {
            background-color: var(--cor-clara);
            padding: 8px 12px;
            margin-top: 10px;
            border-radius: 8px;
            text-align: left;
            position: relative;
            border-left: 4px solid var(--cor-primaria);
        }
        .comentario p { margin: 0 0 4px; word-wrap: break-word; }
        .comentario small { color: var(--texto-suave); font-size: 12px; }

        .inline-form { display: inline-block; margin-left: 10px; vertical-align: middle; }
        .inline-form input[type="text"] {
            padding: 4px 6px; font-size: 13px; border-radius: 4px; border: 1px solid #ccc;
        }
        .inline-form button {
            background-color: var(--cor-primaria); border: none; color: white;
            padding: 5px 10px; font-size: 13px; border-radius: 4px; cursor: pointer;
        }
        .inline-form button:hover { background-color: var(--cor-primaria-hover); }

        form.comentar textarea {
            width: 100%;
            min-height: 60px;
            resize: vertical;
            padding: 8px 10px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        form.comentar button {
            margin-top: 8px;
            background-color: var(--cor-primaria);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        form.comentar button:hover { background-color: var(--cor-primaria-hover); }

        /* Tema escuro (padronizado) */
        body.dark { background: #1e1e1e; color: #f0f0f0; }
        body.dark h2 { color: #fff; }
        body.dark .nav { background-color: #222; }
        body.dark .item { background: #2a2a2a; color: #f0f0f0; box-shadow: 0 6px 14px rgba(0,0,0,0.5); }
        body.dark .comentario { background: #3a3a3a; border-left-color: var(--cor-primaria); }
        body.dark .botao-voltar { background-color: #ff4b2b; }

        @media (max-width: 600px) {
            .nav { flex-direction: column; align-items: flex-start; }
            .nav .links { justify-content: flex-start; margin-top: 10px; }
         h2 {
  width: calc(100vw - 40px); /* 40px = padding-left + padding-right do body */
  margin: 0 auto 30px;
  text-align: center;
  box-sizing: border-box;
}

            .item { max-width: 100%; }
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
        <h2>Serviços Finalizados</h2>

        <a href="painel_funcionario.php" class="botao-voltar">⬅ Voltar</a>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid">
                <?php while($row = $result->fetch_assoc()):
                    $id_contratacao = $row['id'];

                    // Busca comentários da contratação
                    $sql_comentarios = "SELECT * FROM comentarios_servico WHERE id_contratacao = ? ORDER BY data_comentario DESC";
                    $stmt_com = $conn->prepare($sql_comentarios);
                    $stmt_com->bind_param("i", $id_contratacao);
                    $stmt_com->execute();
                    $comentarios = $stmt_com->get_result();

                    $id_usuario_logado = $_SESSION['id'];
                ?>
                <div class="item">
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>

                    <?php if (!empty($row['foto_servico'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['foto_servico']) ?>" alt="Foto do serviço">
                    <?php else: ?>
                        <p><em>Sem foto do serviço enviada.</em></p>
                    <?php endif; ?>

                    <?php while ($comentario = $comentarios->fetch_assoc()): ?>
                        <div class="comentario">
                            <p><?= htmlspecialchars($comentario['comentario']) ?></p>
                            <small><?= date('d/m/Y H:i', strtotime($comentario['data_comentario'])) ?></small>

                            <?php if ($comentario['id_funcionario'] == $id_usuario_logado): ?>
                                <form action="editar_comentario.php" method="POST" class="inline-form">
                                    <input type="hidden" name="comentario_id" value="<?= $comentario['id'] ?>">
                                    <input type="text" name="comentario" value="<?= htmlspecialchars($comentario['comentario']) ?>" required>
                                    <button type="submit">Salvar</button>
                                </form>
                                <form action="excluir_comentario.php" method="POST" class="inline-form">
                                    <input type="hidden" name="comentario_id" value="<?= $comentario['id'] ?>">
                                    <button type="submit" onclick="return confirm('Deseja realmente excluir este comentário?')">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>

                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" class="comentar">
                        <input type="hidden" name="id_contratacao" value="<?= $id_contratacao ?>">
                        <textarea name="comentario" placeholder="Comentar..." required></textarea>
                        <button type="submit">Enviar</button>
                    </form>

                    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($row['data_solicitacao'])) ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="mensagem-vazia">Nenhum serviço finalizado ainda.</p>
        <?php endif; ?>
    </main>

    <script src="../js/tema.js"></script>
</body>
</html>
