<?php
include("conexao.php");
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$contrato_id = $_POST['contrato_id'];
$imagem = $_FILES['imagem_servico'];

$nome_arquivo = uniqid() . "_" . basename($imagem['name']);
$caminho = "../uploads/" . $nome_arquivo;
move_uploaded_file($imagem['tmp_name'], $caminho);

$conn->query("UPDATE contratacoes SET status = 'finalizado', imagem_servico = '$nome_arquivo' WHERE id = $contrato_id");

header("Location: em_atendimento.php");
?>