<?php
// Conexão com o banco
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Recebendo dados
$tipo = $_POST['tipo'] ?? '';
$ocupacao = $_POST['ocupacao'] ?? '';
$comentario = $_POST['comentario'] ?? '';
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$endereco = $_POST['endereco'] ?? '';
$telefone = $_POST['telefone'] ?? '';

if (!$tipo || !$nome || !$email || !$senha || !$endereco || !$telefone) {
    echo "Todos os campos obrigatórios devem ser preenchidos.";
    exit;
}

// Criptografar senha


// Upload da foto de perfil
$fotoPerfil = null;
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
    $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
    $fotoPerfil = uniqid() . "." . $ext;
    move_uploaded_file($_FILES['foto_perfil']['tmp_name'], "../uploads/$fotoPerfil");
}

// Upload do certificado (apenas funcionário)
$certificado = null;
if ($tipo === "funcionario" && isset($_FILES['certificado']) && $_FILES['certificado']['error'] === 0) {
    $ext = pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION);
    $certificado = uniqid() . "." . $ext;
    move_uploaded_file($_FILES['certificado']['tmp_name'], "../uploads/$certificado");
}

// Inserir no banco
$stmt = $conn->prepare("INSERT INTO usuarios (tipo, ocupacao, comentario, nome, email, senha, endereco, telefone, foto_perfil, certificado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $tipo, $ocupacao, $comentario, $nome, $email, $senha, $endereco, $telefone, $fotoPerfil, $certificado);

if ($stmt->execute()) {
    echo "sucesso";
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
