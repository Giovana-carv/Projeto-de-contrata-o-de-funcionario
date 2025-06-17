<?php
session_start();
include("conexao.php");

$cliente_id = $_SESSION['id'];
$funcionario_id = $_POST['funcionario_id'];
$estrelas = $_POST['estrelas'];

// Inserir avaliação
$conn->query("INSERT INTO avaliacoes (id_cliente, id_funcionario, estrelas)
              VALUES ('$cliente_id', '$funcionario_id', '$estrelas')");

// Atualizar média
$media = $conn->query("SELECT AVG(estrelas) AS media FROM avaliacoes WHERE id_funcionario = $funcionario_id")->fetch_assoc()['media'];
$conn->query("UPDATE usuarios SET media_avaliacao = '$media' WHERE id = $funcionario_id");

header("Location: ../cliente/painel_cliente.php");
exit;
?>
