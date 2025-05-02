<?php
$servername = "localhost"; // ou o endereço do seu servidor MySQL
$username = "root"; // seu nome de usuário do banco de dados
$password = ""; // sua senha do banco de dados
$database = "project_servico"; // o nome do seu banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}
echo "Conexão estabelecida com sucesso!";

// Aqui você pode realizar operações no banco de dados

// Fecha a conexão quando terminar
$conn->close();
?>