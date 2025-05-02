<html>
<heads>
<title> Cadastro </title>
</head>
<body>

<h1>Cadastro</h1>
<form method="post" action="">
<label>Nome: </label>
<input name="nome" size = "20" type="text">
<br>
<label>Senha</label>
<input name="senha_pessoa" size = "100" type="text">
<br>
<label>CPF</label>
<input name="CPF" size = "11" type="number">
<br>
<button type="submit" name="enviar"> Cadastrar
</button>
</form>
</body>
</heads>
</html>


<?php
 include "teste_conexao.php";
 if (isset($_POST['enviar'])):
$nome_pessoa = $_POST['nome'];
$senha_pessoa = $_POST['senha_pessoa'];
$cpf = $_POST['CPF'];
$sql = mysqli_query($conexao, "INSERT INTO
pessoa(nome, senha_pessoa, CPF) VALUES('$nome_pessoa','$senha_pessoa','$cpf')");
 endif;

?>
