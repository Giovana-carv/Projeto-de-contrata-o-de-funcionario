<?php
session_start();
include("conexao.php");
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$cliente_id = $_SESSION['id'];
$funcionario_id = $_POST['funcionario_id'];

$conn->query("INSERT INTO contratacoes (cliente_id, funcionario_id, status) VALUES ('$cliente_id', '$funcionario_id', 'pendente')");
//vai inserir dados nas colunas de 'cliente_id', 'funcionario_id' e 'status'  da tabela 'contratacoes'
header("Location: ../cliente/painel_cliente.php");
?>