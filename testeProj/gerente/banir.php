<?php
session_start();
include "../php/conexao.php";

if($_SESSION['cargo'] != 'gerente'); //verifica se o gerente esta logado

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $duracao = $_POST['duracao'];
    $tempo_banido = date('Y-m-d H:i:s', strtotime("+$duracao minutes"));//data de expiração

    //verifica se usuario existe
    $query  = "SELECT * FROM usuarios WHERE email = ?"; //mudar email para id
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email); //mudar email para id
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        //atualiza o status do usuario para banido e define data de expiração
        $update_query = "UPDATE usuarios SET status_banido = 'banido', data_banimento = ?, duracao_banimento = ? WHERE email = ?"; //mudar email para id
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sis", $tempo_banido, $duracao, $email);
        $update_stmt->execute();

        echo "Usuário banido com sucesso.";
    } else{
        echo "Usuário não encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banimento</title>
</head>
<body>
    <form method="POST">
        <label for="email"> E-mail do usuário: </label>
        <input type="email" id="email" name="email" required><br>
        
        <label for="duracao"> Duração do Banimento (em minutos): </label>
        <input type="number" id="duracao" name="duracao" required><br>

        <input type="submit" value="Banir Usuario">
    </form>
</body>
</html>