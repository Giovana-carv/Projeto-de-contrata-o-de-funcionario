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
  <title>Login Gerente</title>
  <style>
      body {
          font-family: Arial, sans-serif;
          background: linear-gradient(135deg, #ff416c, #ff4b2b);
          display: flex;
          justify-content: center;
          align-items: center;
          height: 100vh;
          margin: 0;
      }
      .container {
          background: #fff;
          padding: 2rem;
          border-radius: 12px;
          width: 350px;
          box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      }
      h2 {
          text-align: center;
          margin-bottom: 1.5rem;
          color: #ff4b2b;
      }
      label {
          font-weight: bold;
          display: block;
          margin-top: 10px;
          margin-bottom: 5px;
      }
      input {
          width: 100%;
          padding: 10px;
          border: 1px solid #ccc;
          border-radius: 8px;
      }
      button {
          width: 100%;
          margin-top: 15px;
          padding: 12px;
          background: #ff4b2b;
          color: #fff;
          border: none;
          border-radius: 8px;
          font-size: 1rem;
          cursor: pointer;
          transition: background 0.3s;
      }
      button:hover {
          background: #e94427;
      }
      .link {
          display: block;
          text-align: center;
          margin-top: 10px;
          color: #ff4b2b;
          text-decoration: none;
          font-weight: bold;
      }
      .link:hover {
          text-decoration: underline;
      }
      .erro {
          color: red;
          text-align: center;
          margin-bottom: 10px;
      }
  </style>
</head>
<body>
  <div class="container">
      <h2>Login Gerente</h2>
      <?php if (!empty($erro)) echo "<p class='erro'>$erro</p>"; ?>
      <?php if (isset($_GET['sucesso'])) echo "<p style='color:green;text-align:center'>Cadastro realizado! Faça login.</p>"; ?>
      <form method="POST">
          <label>Email:</label>
          <input type="email" name="email" required>

          <label>Senha:</label>
          <input type="password" name="senha" required>

          <button type="submit">Entrar</button>
          <a href="cadastroGerente.php" class="link">Não tem conta? Cadastre-se</a>
      </form>
  </div>
</body>
</html>
