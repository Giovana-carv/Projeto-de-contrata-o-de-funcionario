<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_funcionario = $_SESSION['id'];

$sql = "SELECT c.*, u.nome AS nome_cliente, u.foto_perfil
        FROM contratacoes c
        JOIN usuarios u ON c.id_cliente = u.id
        WHERE c.id_funcionario = ? AND c.status IN ('pendente', 'em espera')
        ORDER BY c.data_contratacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Solicitações Recebidas</h2>";

while($row = $result->fetch_assoc()):
?>
    <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
        <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>

        <?php if (!empty($row['foto_perfil'])): ?>
            <img src="../uploads/<?= htmlspecialchars($row['foto_perfil']) ?>" alt="Foto de perfil" width="80" height="80" style="border-radius: 50%;">
        <?php else: ?>
            <p>Sem foto de perfil</p>
        <?php endif; ?>

        <?php if (isset($row['data_solicitacao'])): ?>
            <p><strong>Data:</strong> <?= $row['data_solicitacao'] ?></p>
        <?php endif; ?>

        <a href="aceitar.php?id=<?= $row['id'] ?>">✅ Aceitar</a>
        <a href="../php/recusar.php?id=<?= $row['id'] ?>">❌ Recusar</a>
    </div>
<?php endwhile; ?>

    <a href="../funcionario/painel_funcionario.php"> Voltar </a>

