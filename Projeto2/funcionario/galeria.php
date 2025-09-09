<?php
session_start();
include("../php/conexao.php");

// Verifica se o funcionário está logado
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_funcionario = $_SESSION['id'];

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
    <style>
        :root {
            --cor-primaria: #ff4b2b;
            --cor-primaria-hover: #e04326;
            --cor-clara: #fff5f0;
            --sombra-suave: rgba(0, 0, 0, 0.1);
            --texto-padrao: #333;
            --texto-suave: #666;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 2px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            padding: 30px 20px;
            color: var(--texto-padrao);
        }
        h2 {
            text-align: center;
            color: var(--cor-primaria);
            margin-bottom: 30px;
            font-size: 28px;
        }
        .galeria {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .item {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 10px var(--sombra-suave);
            max-width: 250px;
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
        }
        p {
            font-size: 14px;
            color: var(--texto-suave);
            margin-top: 10px;
        }
        .item p strong {
            color: var(--texto-padrao);
        }
        .botao-voltar {
            display: block;
            width: fit-content;
            margin: 0 auto 30px auto;
            background-color: var(--cor-primaria);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .botao-voltar:hover {
            background-color: var(--cor-primaria-hover);
        }
        .mensagem-vazia {
            text-align: center;
            font-size: 16px;
            color: var(--texto-suave);
            margin-top: 40px;
        }
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
        .comentario p {
            margin: 0 0 4px;
            word-wrap: break-word;
        }
        .comentario small {
            color: var(--texto-suave);
            font-size: 12px;
        }
        .inline-form {
            display: inline-block;
            margin-left: 10px;
        }
        .inline-form input[type="text"] {
            padding: 4px 6px;
            font-size: 13px;
            border-radius: 4px;
            border: 1px solid #ccc;
            vertical-align: middle;
        }
        .inline-form button {
            background-color: var(--cor-primaria);
            border: none;
            color: white;
            padding: 5px 10px;
            font-size: 13px;
            border-radius: 4px;
            cursor: pointer;
            vertical-align: middle;
            transition: background-color 0.3s;
        }
        .inline-form button:hover {
            background-color: var(--cor-primaria-hover);
        }
        form.comentar textarea {
            width: 100%;
            min-height: 60px;
            resize: vertical;
            padding: 6px 10px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        form.comentar button {
            margin-top: 6px;
            background-color: var(--cor-primaria);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        form.comentar button:hover {
            background-color: var(--cor-primaria-hover);
        }
    </style>
</head>
<body>
    <h2>Serviços Finalizados</h2>
    <a href="painel_funcionario.php" class="botao-voltar">Voltar</a>

    <?php if ($result->num_rows > 0): ?>
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
    <?php else: ?>
        <p class="mensagem-vazia">Nenhum serviço finalizado ainda.</p>
    <?php endif; ?>
</body>
</html>
