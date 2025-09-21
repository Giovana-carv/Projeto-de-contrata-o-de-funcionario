<?php
include 'conexao.php';

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($nome) && !empty($email)) {
        $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE nome = ? AND email = ?");
        $stmt->bind_param("ss", $nome, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            echo "<script>alert('Sua senha é: " . $row['senha'] . "'); window.location.href = '../html/loginCadastro.html';</script>";
        } else {
            echo "<script>alert('Nome ou email incorretos. Tente novamente.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Preencha todos os campos.');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha de Usuário</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container{
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        input[type="text"], input[type="email"]{
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        button{
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover{
            background-color: #218838;
        }
        a{
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST">
            <input type="text" name="nome" placeholder="Digite seu nome de usuário" required>
            <input type="email" name="email" placeholder="Digite seu email" required>
            <button type="submit"> Recuperar Senha </button>
            <a href="../html/loginCadastro.html">Cancelar</a>
        </form>
    </div>
</body>
</html>