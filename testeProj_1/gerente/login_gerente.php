<?php
session_start(); //inicia ou retorna uma sessão, que pode ser definida por um id via GET ou POST
include("../php/conexao.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
$nome = $_POST['nome'];
$senha = $_POST['senha'];

//verifica se o usuario existe ou se esta banido
$sql = "SELECT * FROM gerentes WHERE nome=? AND senha=?"; //seleciona na tabela 'gerentes' quando 'nome' e 'senha' são usados dados salvos no banco durante cadastro
$stmt = $conn->prepare($sql); //prepare($sql) -> prepara para executar instruções SQL com marcadores de posição
$stmt->bind_param("ss", $nome, $senha); //bind_param("ss", $nome, $senha) -> vincula as variaveis php aos marcadores de posição
$stmt->execute(); //execute -> executa a intrução; stmt -> variavel que armazena o objeto de instrução preparada
$result = $stmt->get_result(); //obtem os resultados

if ($gerente && password_verify($senha, $gerente['senha'])){
    $_SESSION['gerente_id'] = $gerente['id'];
    $_SESSION['gerente_nome'] = $gerente['nome'];
    header("Location: painel_gerente.php");
} else{
    echo "Invalido";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2> Login do Gerente</h2>
    <form method="POST">
        <label> Nome </label>
        <input type="text" name="nome" required><br>

        <label> Senha </label>
        <input type="password" name="senha" required><br>

        <input type="submit" value="Entrar">
    </form>
</body>
</html>