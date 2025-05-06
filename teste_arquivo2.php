<?php
ini_set('default_charset', 'UTF-8');
?>


<html>
<head> <title>Login do Usuário</title>
</head>
<body>
<h1>Login do Usuário</h1>

<form method="post" action="">
<label>Senha</label>
<input name="id" size = "6" type="text">
<br>
<button type="submit" name="confirmar">Confirmar</button>
<button type="submit" name="voltar">Voltar</button>
</form>
</body>
</html>


<?php
include 'conexao.php';
$senha = $_GET['senha'];
$selbanco = mysqli_query($conexao, "SELECT * FROM tbusuarios WHERE
senha='$senha'");
while ($linha=mysqli_fetch_array($selbanco))
{
    
    $xsenha = $linha['senha'];
}
if (isset($_POST['confirmar'])):
$xsenha = $_POST['senha'];
$senhaa = $_POST['senhaa'];

if ($senhaa != $xsenha)
{
    echo "<span>Você não tem permissão para o cadastro!</span>";
    return 0;
}

else 
{
    header("Location:abert_cont.php");
}
endif;

if (isset($_POST['voltar'])):
    header("Location:sist_control.php");
    endif;
?>