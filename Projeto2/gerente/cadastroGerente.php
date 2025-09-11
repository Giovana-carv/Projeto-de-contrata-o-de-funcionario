<?php
include('../php/conexao.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $foto  = $_FILES['foto']['name'] ?? 'default.png';

    // Upload da foto (se enviada)
    if (!empty($_FILES['foto']['name'])) {
        $destino = "uploads/" . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $destino);
    } else {
        $foto = "default.png";
    }

    $sql = "INSERT INTO gerentes (nome, email, senha, foto_perfil) 
            VALUES ('$nome', '$email', '$senha', '$foto')";
    
    if ($conn->query($sql)) {
        echo "Gerente cadastrado com sucesso!";
        echo "<br><a href='loginGerente.php'>Logar</a>";
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Nome:</label>
    <input type="text" name="nome" required><br>

    <label>Email:</label>
    <input type="email" name="email" required><br>

    <label>Senha:</label>
    <input type="password" name="senha" required><br>

    <label>Foto de perfil:</label>
    <input type="file" name="foto"><br><br>

    <button type="submit">Cadastrar</button>
    <a href="loginGerente.php">Logar</a>
</form>
