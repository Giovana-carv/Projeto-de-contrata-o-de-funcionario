<?php
session_start();
include('../php/conexao.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM gerentes WHERE email = '$email' AND senha = '$senha'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows === 1) {
        $gerente = $resultado->fetch_assoc();

        $_SESSION['id_gerente']  = $gerente['id_gerente'];
        $_SESSION['nome']        = $gerente['nome'];
        $_SESSION['email']       = $gerente['email'];
        $_SESSION['foto_perfil'] = $gerente['foto_perfil'];

        header("Location: painelGerente.php");
        exit;
    } else {
        echo "Email ou senha incorretos!";
    }
}
?>

<form method="POST">
    <label>Email:</label>
    <input type="email" name="email" required><br>

    <label>Senha:</label>
    <input type="password" name="senha" required><br><br>

    <button type="submit">Entrar</button>
</form>
