<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_cliente = $_SESSION['id'];

$sql = "SELECT notificacao_cliente, data_contratacao 
        FROM contratacoes 
        WHERE id_cliente = ? AND notificacao_cliente IS NOT NULL
        ORDER BY data_contratacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>🔔 Respostas dos Pedidos</h2>

<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px;">
          <small><?= $row['data_contratacao'] ?></small>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Você não tem notificações.</p>
<?php endif;


// Botão seguro para voltar ao painel
echo "<form action='../cliente/painel_cliente.php' method='get'>
        <button type='submit'>Voltar</button>
      </form>";
?>