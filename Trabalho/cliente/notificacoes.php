<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

include("../php/conexao.php");
$cliente_id = $_SESSION['id'];

$sql = "SELECT c.id, c.status, c.notificacao_cliente, u.nome AS nome_funcionario
        FROM contratacoes c
        JOIN usuarios u ON c.funcionario_id = u.id
        WHERE c.cliente_id = $cliente_id AND c.notificacao_cliente != ''";

$res = $conn->query($sql);

echo "<h2>Notificações</h2>";

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo "<p>
                <strong>{$row['nome_funcionario']}:</strong> {$row['notificacao_cliente']}
                <a href='../php/marcar_lida.php?id={$row['id']}'>Marcar como lida</a>
              </p>";
    }
} else {
    echo "Nenhuma notificação.";
}

// Botão seguro para voltar ao painel
echo "<form action='../cliente/painel_cliente.php' method='get'>
        <button type='submit'>Voltar</button>
      </form>";
?>