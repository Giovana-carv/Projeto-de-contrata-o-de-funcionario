<?php
$host="localhost";
$user="root";
$pass="";
$banco="project_servico";
$conexao=mysqli_connect($host, $user, $pass,
    $banco) or die(mysql_error());
    mysqli_select_db($conexao, $banco) or die(mysql_error());
    ?>