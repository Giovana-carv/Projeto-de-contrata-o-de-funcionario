<?php
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$id = $_GET['id']; //vai usar o id

$conn->query("UPDATE contratacoes SET notificacao_cliente = '' WHERE id = $id"); 
//vai atualizar a tabela 'contratacoes' na coluna 'notificacao_cliente' quando for identificado o 'id'

header("Location: ../cliente/painel_cliente.php");
exit();