<?php
session_start();
include("conexao.php");
if ($_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
$id_func = $_SESSION['id'];

$sql = "SELECT c.id AS contrato_id, u.nome, u.foto_perfil, u.endereco
        FROM contratacoes c
        JOIN usuarios u ON c.cliente_id = u.id
        WHERE c.funcionario_id = $id_func AND c.status = 'aceito'";
$res = $conn->query($sql);
?>

<h2>Em Atendimento</h2>

<?php while($row = $res->fetch_assoc()): ?>
<div class="solicitacao">
    <img src="../uploads/<?php echo $row['foto_perfil']; ?>" width="100">
    <p><strong><?php echo $row['nome']; ?></strong></p>
    <form action="finalizar_servico.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="contrato_id" value="<?= $row['contrato_id'] ?>">
        <label>Foto do serviço:</label><br>
        <input type="file" name="imagem_servico" required><br><br>
        <button type="submit">Finalizar Serviço</button>
    </form>
</div>
<?php endwhile; ?>