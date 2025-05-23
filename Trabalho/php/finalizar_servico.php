<?php
include("conexao.php");
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$id_contratacao = $_POST['id_contratacao'];
$imagem = $_FILES['fotos_servicos'];

$nome_arquivo = uniqid() . "_" . basename($imagem['name']);
$caminho = "../uploads/" . $nome_arquivo;
move_uploaded_file($imagem['tmp_name'], $caminho);

$conn->query("UPDATE contratacoes SET status = 'finalizado', fotos_servicos = '$nome_arquivo' WHERE id = $id_contratacao");

header("Location: em_atendimento.php");
?>