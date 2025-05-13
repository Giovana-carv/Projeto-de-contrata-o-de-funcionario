<?php
include("conexao.php");
// Dados
$nome = $_POST['nome'];
$email = $_POST['email']; //Pode ser alterado
$senha = $_POST['senha'];
$tipo = $_POST['tipo']; //Pode ser alterado
$ocupacao = $_POST['ocupacao'] ?? null;
$comentario = $_POST['comentario'] ?? null; // Pode ser alterado
$endereco = $_POST['endereco']; //Não.

// Uploads
$foto_perfil = $_FILES['foto_perfil']['name']; //Pode ser alterado
$certificado = isset($_FILES['certificado']['name']) ? $_FILES['certificado']['name'] : null; //Pode ser alterado

// Move arquivos
$dir = "../uploads/"; //Pode ser alterado
move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $dir . $foto_perfil); //Pode ser alterado

if ($tipo == "funcionario" && !empty($certificado)) { //Pode ser alterado
    move_uploadaed_file($_FILES['certificado']['tmp_name'], $dir . $certificado);
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

echo "<a href = '../html/loginCadastro.html'>Voltar </a>";

$stmt->close();
$conn->close();
?>