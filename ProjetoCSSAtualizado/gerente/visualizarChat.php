<?php
session_start();
include("../php/conexao.php");

// Verifica se o gerente está logado
if (!isset($_SESSION['id_gerente'])) {
    echo "Acesso negado.";
    exit;
}

// Recebe o ID do denunciado via POST
if (!isset($_POST['id_denunciado'])) {
    echo "Usuário não especificado.";
    exit;
}
$id_denunciado = (int)$_POST['id_denunciado'];

// Busca a denúncia correspondente para obter o denunciante
$sql = "SELECT id_denunciante, id_denunciado 
        FROM denuncias 
        WHERE id_denunciado = ? 
        ORDER BY data_denuncia DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_denunciado);
$stmt->execute();
$res = $stmt->get_result();
$denuncia = $res->fetch_assoc();

if (!$denuncia) {
    echo "Denúncia não encontrada.";
    exit;
}

$id_denunciante = $denuncia['id_denunciante'];

// Busca nomes dos dois usuários
$u_sql = "SELECT id, nome, tipo, foto_perfil FROM usuarios WHERE id IN (?, ?)";
$u_stmt = $conn->prepare($u_sql);
$u_stmt->bind_param("ii", $id_denunciante, $id_denunciado);
$u_stmt->execute();
$u_res = $u_stmt->get_result();

$usuarios = [];
while ($u = $u_res->fetch_assoc()) {
    $usuarios[$u['id']] = $u;
}

// Busca as mensagens trocadas entre eles
$msg_sql = "SELECT * FROM mensagens 
            WHERE (remetente_id = ? AND destinatario_id = ?) 
               OR (remetente_id = ? AND destinatario_id = ?)
            ORDER BY data_envio ASC";
$msg_stmt = $conn->prepare($msg_sql);
$msg_stmt->bind_param("iiii", $id_denunciante, $id_denunciado, $id_denunciado, $id_denunciante);
$msg_stmt->execute();
$msgs = $msg_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Visualizar Conversa</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 20px;
}
.chat-container {
    max-width: 700px;
    margin: auto;
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0 8px rgba(0,0,0,0.2);
}
h2 {
    text-align: center;
    color: #333;
}
.mensagem {
    margin: 8px 0;
    padding: 10px;
    border-radius: 8px;
    max-width: 75%;
    word-wrap: break-word;
}
.enviada {
    background: #d1ffd6;
    margin-left: auto;
    text-align: right;
}
.recebida {
    background: #e6e6e6;
    margin-right: auto;
}
small {
    display: block;
    font-size: 11px;
    color: #555;
}
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}
.user-info img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #888;
}
.voltar {
    display: inline-block;
    background: #004aad;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
}
.voltar:hover {
    background: #00337f;
}
</style>
</head>
<body>
<div class="chat-container">
    <div class="header">
        <div class="user-info">
            <?php if (!empty($usuarios[$id_denunciante]['foto_perfil'])): ?>
                <img src="../uploads/<?= htmlspecialchars($usuarios[$id_denunciante]['foto_perfil']) ?>" alt="Foto denunciante">
            <?php endif; ?>
            <strong><?= htmlspecialchars($usuarios[$id_denunciante]['nome']) ?> (Denunciante)</strong>
        </div>

        <div class="user-info">
            <?php if (!empty($usuarios[$id_denunciado]['foto_perfil'])): ?>
                <img src="../uploads/<?= htmlspecialchars($usuarios[$id_denunciado]['foto_perfil']) ?>" alt="Foto denunciado">
            <?php endif; ?>
            <strong><?= htmlspecialchars($usuarios[$id_denunciado]['nome']) ?> (Denunciado)</strong>
        </div>
    </div>

    <h2>Histórico da conversa</h2>

    <?php if ($msgs->num_rows > 0): ?>
        <?php while ($m = $msgs->fetch_assoc()): ?>
            <div class="mensagem <?= ($m['remetente_id'] == $id_denunciante) ? 'enviada' : 'recebida' ?>">
                <?= nl2br(htmlspecialchars($m['mensagem'])) ?>
                <small><?= date('d/m/Y H:i', strtotime($m['data_envio'])) ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; color:#777;">Nenhuma mensagem trocada entre esses usuários.</p>
    <?php endif; ?>

    <br>
    <a href="painelDenuncias.php" class="voltar">⬅ Voltar</a>
</div>
</body>
</html>
