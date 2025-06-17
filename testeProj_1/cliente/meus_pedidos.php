<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_cliente = $_SESSION['id'];

$sql = "SELECT c.*, u.nome AS nome_funcionario
        FROM contratacoes c
        JOIN usuarios u ON c.id_funcionario = u.id
        WHERE c.id_cliente = ?
        ORDER BY c.data_contratacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Meus Pedidos</h2>
<?php while($row = $result->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
        <p><strong>Funcionário:</strong> <?= htmlspecialchars($row['nome_funcionario']) ?></p>
        <p><strong>Status:</strong> <?= ucfirst($row['status']) ?></p>
        <?php if ($row['notificacao_cliente']): ?>
            <p><strong>Mensagem:</strong> <?= htmlspecialchars($row['notificacao_cliente']) ?></p>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
