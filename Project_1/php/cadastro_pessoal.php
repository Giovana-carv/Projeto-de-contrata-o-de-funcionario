<?php
include("conexao.php");
// Dados
$nome = $_POST['nome'];
$cpf = $_POST['cpf']; // Pode ser alterado
$cnpj = $_POST['cnpj']; // Pode ser alterado
$email = $_POST['email']; //Pode ser alterado
$senha = $_POST['senha'];
$ocupacao = $_POST['ocupacao'];
$endereco_pes = $_POST['endereco_pes']; //Não.
$endereco_cnpj = $_POST['endereco_cnpj']; //Não.

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
$sql = "INSERT INTO pessoa (nome, email, senha, tipo, ocupacao, comentario, endereco, foto_perfil, certificado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
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