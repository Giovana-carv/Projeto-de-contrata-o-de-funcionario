<?php
include("conexao.php");
// Dados
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$tipo = $_POST['tipo'];
$ocupacao = $_POST['ocupacao'] ?? null;
$comentario = $_POST['comentario'] ?? null;
$endereco = $_POST['endereco'];

// Uploads
$foto_perfil = $_FILES['foto_perfil']['name'];
$certificado = isset($_FILES['certificado']['name']) ? $_FILES['certificado']['name'] : null;

// Move arquivos
$dir = "../uploads/";
move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $dir . $foto_perfil);

if ($tipo == "funcionario" && !empty($certificado)) {
    move_uploaded_file($_FILES['certificado']['tmp_name'], $dir . $certificado);
} else {
    $certificado = null;
}

// Inserção
$sql = "INSERT INTO usuarios (nome, email, senha, tipo, ocupacao, comentario, endereco, foto_perfil, certificado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $nome, $email, $senha, $tipo,  $ocupacao, $comentario, $endereco, $foto_perfil, $certificado);

if ($stmt->execute()) {
   echo "Cadastro realizado com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}


$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    *{
        padding: 4;
        margin: 3;
        box-sizing: border-box solid black;
        font-family: 'Inter', sans-serif;
        text-align: center;
    }
    </style>
</head>
<body>
    <form>
        <button><a href="../html/loginCadastro.html">Voltar</a></button>
    </form>
</body>
</html>