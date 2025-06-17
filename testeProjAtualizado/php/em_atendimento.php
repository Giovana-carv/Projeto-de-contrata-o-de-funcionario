<?php
session_start();
include("conexao.php");

$id_funcionario = $_SESSION['id'];

$sql = "SELECT c.*, u.nome AS nome_cliente, u.foto_perfil
        FROM contratacoes c
        JOIN usuarios u ON c.id_cliente = u.id
        WHERE c.id_funcionario = ? AND c.status = 'em atendimento'
        ORDER BY c.data_contratacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Serviços em Atendimento</h2>

<?php while($row = $result->fetch_assoc()): ?>
    <div>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>

        <img src="../uploads/<?php echo htmlspecialchars($row['foto_perfil']); ?>" alt="Foto do Cliente" width="100" height="100" style="border-radius: 50%;"><br>

        <form action="../php/finalizar_servico.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_contratacao" value="<?= $row['id'] ?>">
            <label>Foto do serviço finalizado:</label>
            <input type="file" name="foto_servico" required>
            <button type="submit">Finalizar Serviço</button>
        </form>
    </div>
<?php endwhile; ?>

<a href="../funcionario/painel_funcionario.php">Voltar</a>
