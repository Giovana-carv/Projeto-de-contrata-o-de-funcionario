<?php
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$id = $_GET['id'];
$conn->query("UPDATE contratacoes SET status = 'aceito', notificacao_cliente = 'Seu pedido foi aceito pelo funcionario.' WHERE id=$id");
header("Location: solicitacoes.php");
?>