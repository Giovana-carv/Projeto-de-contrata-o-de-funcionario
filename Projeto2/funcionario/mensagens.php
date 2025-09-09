<?php 
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$funcionario_id = $_SESSION['id'];

// Agora buscando também a foto_perfil diretamente
$sql = "SELECT DISTINCT u.id, u.nome, u.foto_perfil 
        FROM mensagens m 
        JOIN usuarios u ON ((m.remetente_id = u.id OR m.destinatario_id = u.id) AND u.tipo = 'cliente') 
        WHERE (m.remetente_id = ? OR m.destinatario_id = ?) AND u.id != ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $funcionario_id, $funcionario_id, $funcionario_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Funcionário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
        }
        .info {
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 12px #ff416c;
        }
        .cliente {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .cliente img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #aaa;
        }
    </style>
</head>
<body>
    <h2>Notificações</h2>

    <a href="../funcionario/painel_funcionario.php">Voltar</a>
    <?php while ($cliente = $result->fetch_assoc()): ?>
        <div class="info">
            <div class="cliente">
                <?php if (!empty($cliente['foto_perfil'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($cliente['foto_perfil']); ?>" alt="foto de perfil">
                <?php else: ?>
                    <img src="../imagens/perfil_padrao.png" alt="sem foto">
                <?php endif; ?>

                <a href="../chat/chat.php?destinatario_id=<?= $cliente['id'] ?>">
                    <?= htmlspecialchars($cliente['nome']) ?>
                </a>
            </div>
        </div>
    <?php endwhile; ?>

</body>
</html>
