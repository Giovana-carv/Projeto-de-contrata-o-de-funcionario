<?php
// Configurações do banco de dados
$host = "localhost";
$user = "root";
$pass = "";
$db = "sistema_usuarios";

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $db);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
//$conn->connect_error: vai exibir o erro
?>