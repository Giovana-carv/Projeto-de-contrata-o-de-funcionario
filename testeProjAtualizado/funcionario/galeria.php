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
        ORDER BY c.data_contratacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Galeria de Serviços Finalizados</title>
    <style>
                body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        .galeria {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .item {
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 250px;
            text-align: center;
        }
        .item img {
            width: 100%;
            border-radius: 8px;
        }
        p {
            font-size: 14px;
            color: #555;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <h2>Serviços Finalizados</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="item">
                <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>
                <?php if (!empty($row['foto_servico'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($row['foto_servico']) ?>" alt="Foto do serviço">
                <?php else: ?>
                    <p><em>Sem foto do serviço enviada.</em></p>
                <?php endif; ?>
                <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($row['data_contratacao'])) ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum serviço finalizado ainda.</p>
    <?php endif; ?>

       <button> <a href="../funcionario/painel_funcionario.php"> Voltar </a></button>

</body>
</html>
