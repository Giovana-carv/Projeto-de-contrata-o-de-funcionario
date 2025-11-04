<?php
include('../php/conexao.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $foto  = $_FILES['foto']['name'] ?? 'default.png';

    if (!empty($_FILES['foto']['name'])) {
        $destino = "../uploads/" . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $destino);
    } else {
        $foto = "default.png";
    }

    $sql = "INSERT INTO gerentes (nome, email, senha, foto_perfil) 
            VALUES ('$nome', '$email', '$senha', '$foto')";

    if ($conn->query($sql)) {
        header("Location: loginGerentev.php?sucesso=1");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro Gerente</title>
  <style>
      body {
          font-family: Arial, sans-serif;
          background: linear-gradient(135deg, #ff4b2b, #ff416c);
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

      .file-upload {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 5px;
}

.file-label {
    background: #ff4b2b;
    color: #fff;
    padding: 8px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.file-label:hover {
    background: #e94427;
}

#file-chosen {
    font-size: 0.9rem;
    color: #555;
}

  </style>
</head>
<body>
  <div class="container">
      <h2>Cadastrar Gerente</h2>
      <form method="POST" enctype="multipart/form-data">
          <label>Nome:</label>
          <input type="text" name="nome" required>

          <label>Email:</label>
          <input type="email" name="email" required>

          <label>Senha:</label>
          <input type="password" name="senha" required>

          <label>Foto de perfil:</label>
<div class="file-upload">
    <input type="file" name="foto" id="foto" hidden>
    <label for="foto" class="file-label">Escolher arquivo</label>
    <span id="file-chosen">Nenhum arquivo selecionado</span>
</div>


          <button type="submit">Cadastrar</button>
          <a href="loginGerentev.php" class="link">Já tem conta? Entrar</a>
      </form>
  </div>

  <script>
  const inputFile = document.getElementById("foto");
  const fileChosen = document.getElementById("file-chosen");

  inputFile.addEventListener("change", function() {
    fileChosen.textContent = this.files.length > 0 ? this.files[0].name : "Nenhum arquivo selecionado";
  });
</script>

</body>
</html>
