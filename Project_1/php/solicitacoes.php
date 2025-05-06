<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] != 'funcionario') {
    echo "Acesso negado.";
    exit;
}

$id_func = $_SESSION['id'];

$sql = "SELECT c.id, u.nome, u.endereco, u.foto_perfil 
        FROM contratacoes c
        JOIN usuarios u ON c.cliente_id = u.id
        WHERE c.funcionario_id = $id_func AND c.status = 'pendente'";

$res = $conn->query($sql);

echo "<h2>Solicitações Pendentes</h2>";

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo "<div style='margin-bottom: 20px; border:1px solid #ccc; padding:10px;'>";
        echo "<img src='../uploads/{$row['foto_perfil']}' width='80'><br>";
        echo "<strong>Cliente:</strong> " . $row['nome'] . "<br>";
        echo "<strong>Endereço:</strong> " . $row['endereco'] . "<br>";
        echo "<a href='aceitar.php?id={$row['id']}'>Aceitar</a> | ";
        echo "<a href='recusar.php?id={$row['id']}'>Recusar</a>";
        echo "</div>";
    }
} else {
    echo "Nenhuma solicitação no momento.";
}
?>