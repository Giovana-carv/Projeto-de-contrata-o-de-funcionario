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
        WHERE c.id_funcionario = ? AND c.status = 'em atendimento' AND c.acao = 'aceito'
        ORDER BY c.data_solicitacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Serviços em Atendimento</h2>

<?php while($row = $result->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 15px; margin: 10px;">
        <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>

        <?php if (!empty($row['foto_perfil'])): ?>
            <img src="../uploads/<?= htmlspecialchars($row['foto_perfil']) ?>" 
                 alt="Foto do Cliente" width="80" height="80" style="border-radius: 50%;">
        <?php else: ?>
            <p>Sem foto de perfil</p>
        <?php endif; ?>

        <form action="../php/finalizar_servico.php" method="POST" enctype="multipart/form-data" style="margin-top: 10px;">
            <input type="hidden" name="id_contratacao" value="<?= $row['id'] ?>">
            <label for="foto_servico">Foto do serviço finalizado:</label><br>
            <input type="file" name="foto_servico" id="foto_servico" required><br><br>
            <button type="submit">✅ Finalizar Serviço</button>
        </form>
    </div>
<?php endwhile; ?>

<a href="../funcionario/painel_funcionario.php">🔙 Voltar</a>
