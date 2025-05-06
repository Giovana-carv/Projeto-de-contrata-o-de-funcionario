<?php
//include("conexao.php");
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$contrato_id = $_POST['contrato_id'];
$acao = $_POST['acao']; // 'aceito' ou 'recusado'

$conn->query("UPDATE contratacoes SET status = '$acao' WHERE id = $contrato_id");

header("Location: ../funcionario/painel_funcionario.php");
?>