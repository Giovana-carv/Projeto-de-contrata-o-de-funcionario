<?php 
session_start();
include("conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_funcionario = $_SESSION['id'];

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
    <title>Solicitações</title>
    <!-- Tema claro/escuro -->
    <style>
/* ====== Layout Geral ====== */
body {
    font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    padding: 20px;
    background: var(--background, #f5f5f5);
    color: var(--text, #333);
    transition: background 0.3s ease, color 0.3s ease;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 26px;
    color: var(--accent, #444);
}

/* ====== Card de Solicitação ====== */
.pedido-container {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 20px;
    margin: 15px auto;
    max-width: 600px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.pedido-container:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

/* ====== Foto do Cliente ====== */
.pedido-container img {
    border-radius: 50%;
    width: 80px;
    height: 80px;
    object-fit: cover;
    margin: 10px 0;
    border: 3px solid var(--accent, #444);
}

/* ====== Botões ====== */
.botao-aceitar,
.botao-recusar {
    border: none;
    padding: 10px 18px;
    font-size: 15px;
    border-radius: 8px;
    cursor: pointer;
    margin-right: 8px;
    display: inline-block;
    transition: background 0.3s ease, transform 0.2s ease;
    text-align: center;
}

.botao-aceitar {
    background: #28a745;
    color: #fff;
    text-decoration: none;
}
.botao-aceitar:hover {
    background: #218838;
    transform: scale(1.05);
}

.botao-recusar {
    background: #dc3545;
    color: #fff;
    text-decoration: none;
}
.botao-recusar:hover {
    background: #b02a37;
    transform: scale(1.05);
}

/* ====== Link Voltar ====== */
a[name="voltar"] {
    display: block;
    text-align: center;
    margin-top: 25px;
    color: var(--accent, #444);
    text-decoration: none;
    font-weight: bold;
}
a[name="voltar"]:hover {
    text-decoration: underline;
}

/* ====== Botão Tema ====== */
.theme-toggle {
    position: fixed;
    top: 15px;
    right: 15px;
    border: none;
    background: var(--card-bg, #fff);
    border-radius: 50%;
    padding: 10px;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: background 0.3s ease, transform 0.2s ease;
}
.theme-toggle:hover {
    transform: rotate(20deg);
}

 /*Tema escuro*/
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
    body.dark a{
        color: white;
    }

    </style>
</head>
<body>
    <button class="theme-toggle" id="theme-toggle">🌙</button>

    <h2>Solicitações Recebidas</h2>

    <?php while($row = $result->fetch_assoc()): ?>
        <div class="pedido-container">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>

            <?php if (!empty($row['foto_perfil'])): ?>
                <img src="../uploads/<?= htmlspecialchars($row['foto_perfil']) ?>" alt="Foto de perfil" width="100" height="100" style="border-radius: 50%;">
            <?php else: ?>
                <p>Sem foto de perfil</p>
            <?php endif; ?>

            <?php if (isset($row['data_solicitacao'])): ?>
                <p><strong>Data da solicitação:</strong> <?= date('d/m/Y H:i', strtotime($row['data_solicitacao'])) ?></p>
            <?php endif; ?>

            <a href="aceitar.php?id=<?= $row['id'] ?>" class="botao-aceitar"> Aceitar</a>
            <a href="../php/recusar.php?id=<?= $row['id'] ?>" class="botao-recusar"> Recusar</a>
        </div>
    <?php endwhile; ?>

    <a href="../funcionario/painel_funcionario.php" name="voltar">Voltar</a>

    <!-- Script do tema -->
    <script src="../js/tema.js"></script>
</body>
</html>
