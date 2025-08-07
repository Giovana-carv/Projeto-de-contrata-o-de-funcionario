<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$funcionario_id = $_SESSION['id'];

// Seleciona os clientes que trocaram mensagem com o funcionário
$sql = "SELECT DISTINCT u.id, u.nome 
        FROM mensagens m
        JOIN usuarios u ON 
          (u.id = m.remetente_id OR u.id = m.destinatario_id)
        WHERE (m.remetente_id = ? OR m.destinatario_id = ?) 
          AND u.tipo = 'cliente'
          AND u.id != ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $funcionario_id, $funcionario_id, $funcionario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Clientes com mensagens</h2>
<ul>
<?php while($cliente = $result->fetch_assoc()): ?>
    <li>
        <a href="chat.php?destinatario_id=<?= $cliente['id'] ?>">
            <?= htmlspecialchars($cliente['nome']) ?>
        </a>
    </li>
<?php endwhile; ?>
</ul>
