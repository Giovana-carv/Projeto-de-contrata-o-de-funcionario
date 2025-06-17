<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_funcionario = $_SESSION['id'];

$sql = "SELECT c.*, u.nome AS nome_cliente
        FROM contratacoes c
        JOIN usuarios u ON c.id_cliente = u.id
        WHERE c.id_funcionario = ? AND c.status = 'finalizado'
        ORDER BY c.data_contratacao DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Serviço Finalizado</h2>

<?php if ($row = $result->fetch_assoc()): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>

        <?php if (!empty($row['foto_servico'])): ?>
            <img src="../uploads/<?= htmlspecialchars($row['foto_servico']) ?>" width="200">
        <?php else: ?>
            <p>Sem foto registrada</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <p>Nenhum serviço finalizado encontrado.</p>
<?php endif; ?>

<a href="em_atendimento.php">Voltar</a>
