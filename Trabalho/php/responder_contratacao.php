<?php
//include("conexao.php");
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$id_contratacao = $_POST['id_contratacao'];
$acao = $_POST['acao']; // 'aceito' ou 'recusado'

$conn->query("UPDATE contratacoes SET status = '$acao' WHERE id = $id_contratacao");

header("Location: ../funcionario/painel_funcionario.php");
?>