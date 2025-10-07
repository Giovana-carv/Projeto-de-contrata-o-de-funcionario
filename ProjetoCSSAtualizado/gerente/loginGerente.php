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
        $erro = "Email ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login do Gerente</title>
<style>
/* ======== BASE ======== */
* {
  box-sizing: border-box;
}
body {
  margin: 0;
  height: 100vh;
  background: linear-gradient(135deg, #c0392b, #e74c3c);
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: "Segoe UI", Arial, sans-serif;
  color: #333;
}

/* ======== CONTAINER ======== */
.container {
  background: #fff;
  border-radius: 20px;
  padding: 40px 35px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
  text-align: center;
  transition: all 0.4s ease;
}

/* ======== TÍTULO ======== */
h2 {
  margin-bottom: 25px;
  color: #e74c3c;
  font-weight: 700;
}

/* ======== CAMPOS ======== */
label {
  display: block;
  text-align: left;
  font-weight: 600;
  margin-bottom: 5px;
  color: #444;
}
input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 10px;
  margin-bottom: 15px;
  font-size: 15px;
  transition: border-color 0.3s ease;
}
input:focus {
  border-color: #c0392b;
  outline: none;
}

/* ======== BOTÃO ======== */
button {
  width: 100%;
  padding: 12px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: bold;
  text-transform: uppercase;
  cursor: pointer;
  transition: background 0.3s ease;
}
button:hover {
  background: #c0392b;
}

/* ======== MENSAGEM DE ERRO ======== */
.erro {
  background: #ffe5e5;
  color: #c0392b;
  padding: 10px;
  border-radius: 10px;
  margin-bottom: 15px;
  font-weight: 600;
}
</style>
</head>

<body>
<div class="container">
    <h2>Login do Gerente</h2>

    <?php if (isset($erro)): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>
    </form>
</div>

</body>
</html>