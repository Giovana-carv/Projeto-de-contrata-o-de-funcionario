<?php
include("../php/conexao.php");
// Dados
if($_SERVER['REQUEST_METHOD'] == 'POST'){
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

// Uploads
$foto_perfil = $_FILES['foto_perfil']['name'];

// Move arquivos
$dir = "../uploads/";
move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $dir . $foto_perfil);

// Inserção
$sql = "INSERT INTO gerentes (nome, email, senha) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senha);

if ($stmt->execute()) {
   echo "Gerente cadastro com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}
}

echo "<a href = 'login_gerente.html'>Logar</a>";
?>

<form method="POST">
    <input type="text" name="nome" placeholder="Nome" required />
    <input type="email" name="email" placeholder="E-mail" required />
    <input type="password" name="senha" placeholder="Senha" required />
    
    <label>Foto de Perfil</label>
    <input type="file" name="foto_perfil" accept="image/*" required />

    <input type="submit" value="Entrar">

</form>