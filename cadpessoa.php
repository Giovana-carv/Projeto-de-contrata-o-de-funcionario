<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Cliente</title>
    <link rel="stylesheet" href="cadcliente.css">
</head>
<body>
    <div class="container">
        <header class="header-content">
            <img src="imagens/imagem_1.jpeg" alt="Mulher trabalhando no computador" class="img-logo" />
            <div class="header-text">
                <h1><span>Contrate um</span> <br>Profissional</h1>
            </div>
        </header>
        <main class="cadastro-form">
            <h2 class="form-title">Cadastro Cliente</h2>
            <form id="cadastro-form">
                <fieldset>
                    <legend>Informações Pessoais</legend>
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                    <label for="endereco">CPF:</label>
                    <input type="text" id="cpf" name="cpf" placeholder="Digite seu endereço" required>

                    <label for="estado">CNPJ:</label>
                    <input type="text" id="cnpj" name="cnpj" placeholder="Digite seu estado" required>

            

                <button type="submit" class="button" name="enviar">Cadastrar</button>
            </form>
        </main>
    </div>
</body>
</html>

<?php
include("conexao.php");
 if (isset($_POST['enviar'])):
$nome = $_POST['nome'];
$senha = $_POST['senha'];
$cpf = $_POST['cpf'];
$cnpj = $_POST['cnpj'];
$sql = mysqli_query($conexao, "INSERT INTO
pessoa(nome, senha, cpf, cnpj) VALUES('$nome','$senha', '$cpf','$cnpj')");
 endif;

?>
