<?php
include("conexao.php");

if (isset($_POST['enviar'])):
    $nome_pessoa = $_POST['nome'];
    $senha_pessoa = $_POST['senha_pessoa'];
    $cpf = $_POST['CPF'];
    $cnpj = $_POST['cnpj'];
    $sql = mysql_query($conn, "INSERT INTO pessoa(nome, senha_pessoa, CPF, cnpj) VALUES('$nome_pessoa','$senha_pessoa','$cpf', '$cnpj')");
endif;
echo(mysqli_query);
?> 

