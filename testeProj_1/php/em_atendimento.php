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

// Buscar foto do perfil
$sqlFoto = "SELECT foto_perfil FROM usuarios WHERE id = ?";
$stmtFoto = $conn->prepare($sqlFoto);
$stmtFoto->bind_param("i", $id_funcionario);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = ($resFoto && $resFoto->num_rows > 0) ? $resFoto->fetch_assoc() : ['foto_perfil' => 'default.png'];

?>

<h2>Serviços em Atendimento</h2>
<?php while($row = $result->fetch_assoc()): ?>
    <div>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($row['nome_cliente']) ?></p>
        <form action="../php/finalizar_servico.php" method="POST" enctype="multipart/form-data">
        <img src="../uploads/<?php echo htmlspecialchars($foto['foto_perfil']); ?>" alt="foto de perfil">

            <input type="hidden" name="id_contratacao" value="<?= $row['id'] ?>">
            <input type="file" name="foto_servico" required>
            <button type="submit">Finalizar Serviço</button>
        </form>
    </div>
<?php endwhile; ?>
    <a href="../funcionario/painel_funcionario.php"> Voltar </a>

