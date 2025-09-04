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

echo "<h2>Solicitações Recebidas</h2>";

while($row = $result->fetch_assoc()):
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações</title>
    <style>
       .pedido-container {
        border: 1px solid #ccc;
        margin: 10px;
        padding: 10px;
        transition: transform 0.3s ease;
    }
    .pedido-container:hover {
        transform: translateX(10px);
    }
    .botao-aceitar, .botao-recusar {
        border: none;
        color: white;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 14px;
        border-radius: 6px;
        margin-right: 5px;
        text-decoration: none;
        display: inline-block;
    }
    .botao-aceitar {
        background-color: green;
    }
    .botao-aceitar:hover {
        background-color: #136413ff;
    }
    .botao-recusar {
        background-color: red;
    }
    .botao-recusar:hover {
        background-color: #b30000;
    }
    .botao-aceitar a, .botao-recusar a {
        color: white;
        text-decoration: none;
    }
    </style>
</head>
<body>
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

    
</body>
</html>
