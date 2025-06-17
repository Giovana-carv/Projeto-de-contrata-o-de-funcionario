<?php
session_start();
include "../php/conexao.php";

if($_SESSION['cargo'] != 'gerente'); //verifica se o gerente esta logado

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email']; //mudar email para id

    //verifica se usuario existe e se esta banido
    $query = "SELECT * FROM usuarios WHERE email = ? AND status_banido = 'banido'"; //mudar email para id
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email); //mudar email para id
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        //atualiza o status para 'ativo' e limpa a data de banimento
        $update_query = "UPDATE usuarios SET status_banido = 'ativo', data_banimento = NULL, duracao_banimento = NULL WHERE email = ?"; //mudar email para id
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("s", $email);
        $update_stmt->execute();

        echo "Usuário desbanido com sucesso.";
    } else{
        echo "Usuário não encontrado ou não está banido";
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

        <input type="submit" value="Desbanir Usuario">
    </form>
</body>
</html>