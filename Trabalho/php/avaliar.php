<?php
session_start();
include("conexao.php");
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$cliente_id = $_SESSION['id'];
$funcionario_id = $_POST['funcionario_id'];
$estrelas = $_POST['estrelas'];

$conn->query("INSERT INTO avaliacoes (cliente_id, funcionario_id, estrelas)
              VALUES ('$cliente_id', '$funcionario_id', '$estrelas')");

// Atualizar média de avaliação
$media = $conn->query("SELECT AVG(estrelas) AS media FROM avaliacoes WHERE funcionario_id = $funcionario_id")->fetch_assoc()['media'];
$conn->query("UPDATE usuarios SET media_avaliacao = '$media' WHERE id = $funcionario_id");

header("Location: ../cliente/painel_cliente.php");
?>