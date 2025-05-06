<?php
session_start();
include("conexao.php");
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
$id_func = $_SESSION['id'];

$sql = "SELECT u.nome, c.imagem_servico FROM contratacoes c
        JOIN usuarios u ON c.cliente_id = u.id
        WHERE c.funcionario_id = $id_func AND c.status = 'finalizado'";
$res = $conn->query($sql);
?>

<h2>Serviços Antigos</h2>

<?php while($row = $res->fetch_assoc()): ?>
<div class="servico">
    <p><strong>Cliente:</strong> <?= $row['nome'] ?></p>
    <img src="../uploads/<?= $row['imagem_servico'] ?>" width="200">
</div>
<?php endwhile; //fim do bloco de codigo controlado pelo WHILE ?>