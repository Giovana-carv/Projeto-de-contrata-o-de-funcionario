<?php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco_de_dados = "project_servico";

// Criar uma conexão
$conn = new mysqli($host, $usuario, $senha, $banco_de_dados);

// Verificar se a conexão foi estabelecida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>