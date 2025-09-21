<?php
session_start();
if ($_SESSION['tipo'] !== 'cliente' && $_SESSION['tipo'] !== 'funcionario') {
    echo "Acesso restrito.";
    exit;
}

include("../php/conexao.php");

$tipo = $_SESSION['tipo'];
$usuarios = ($tipo == 'cliente') ? 'cliente' : 'funcionario';

// Verifica login
if (!isset($_SESSION['id'])) {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$usuario_id = $_SESSION['id'];

// Pega o destinatário pelo GET
if (!isset($_GET['destinatario_id']) || !is_numeric($_GET['destinatario_id'])) {
    echo "Destinatário inválido.";
    exit;
}
$destinatario_id = (int)$_GET['destinatario_id'];

// Pega nome do destinatário
$destinatario = $conn->prepare("SELECT nome, foto_perfil FROM usuarios WHERE id = ?");
$destinatario->bind_param("i", $destinatario_id);
$destinatario->execute();
$res = $destinatario->get_result();

$dados_destinatario = $res->fetch_assoc();

if (!$dados_destinatario) {
    echo "Usuário não encontrado.";
    exit;
}

$nome_destinatario = $dados_destinatario['nome'];
$foto_destinatario = $dados_destinatario['foto_perfil'];

// Mensagens
$msg_stmt = $conn->prepare("SELECT * FROM mensagens WHERE 
    (remetente_id = ? AND destinatario_id = ?) OR 
    (remetente_id = ? AND destinatario_id = ?) ORDER BY data_envio ASC");
$msg_stmt->bind_param("iiii", $usuario_id, $destinatario_id, $destinatario_id, $usuario_id);
$msg_stmt->execute();
$msg_result = $msg_stmt->get_result();

//denuncia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_denunciante = $_SESSION['id'];
    $id_denunciado = intval($_POST['denunciado_id']);
    $motivo = trim($_POST['motivo']);

    if ($id_denunciante === $id_denunciado) {
        echo "Você não pode se denunciar.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO denuncias (id_denunciante, id_denunciado, motivo) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_denunciante, $id_denunciado, $motivo);

    if ($stmt->execute()) {
        echo "<script>alert('Denúncia registrada com sucesso!'); window.location.href='../chat/chat.php?destinatario_id={$id_denunciado}';</script>";
    } else {
        echo "Erro ao registrar denúncia.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat com <?php echo htmlspecialchars($nome_destinatario); ?></title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .chat-box { max-width: 600px; margin: auto; background: white; padding: 15px; border-radius: 8px; }
        .mensagem { margin: 10px 0; padding: 10px; border-radius: 5px; max-width: 80%; }
        .enviada { background: #d1ffd6; margin-left: auto; text-align: right; }
        .recebida { background: #e6e6e6; margin-right: auto; }
        form { margin-top: 20px; display: flex; gap: 10px; }
        textarea { flex: 1; resize: none; padding: 8px; border-radius: 5px; }
        button { padding: 10px 20px; background: #28a745; border: none; color: white; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="chat-box">
        <!-- Botão de denúncia -->
<form action="../php/denunciar.php" method="POST" style="margin-top: 15px;">
    <input type="hidden" name="denunciado_id" value="<?php echo $destinatario_id; ?>">
    <textarea name="motivo" required placeholder="Explique o motivo da denúncia..."></textarea>
    <button type="submit" style="background: #dc3545; padding: 10px; border: none; color: white; border-radius: 5px; cursor: pointer;">
        Denunciar <?php echo htmlspecialchars($nome_destinatario); ?>
    </button>
</form>

        <h3><?php echo htmlspecialchars($nome_destinatario); ?></h3>
        <?php if (!empty($foto_destinatario)): ?>
    <img src="../uploads/<?php echo htmlspecialchars($foto_destinatario); ?>" 
         alt="Foto de <?php echo htmlspecialchars($nome_destinatario); ?>" 
         style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #888; margin-bottom: 10px;">
<?php endif; ?>      
        <?php while($m = $msg_result->fetch_assoc()): ?>
            <div class="mensagem <?php echo $m['remetente_id'] == $usuario_id ? 'enviada' : 'recebida'; ?>">
                <?php echo nl2br(htmlspecialchars($m['mensagem'])); ?>
                <br><small><?php echo date('d/m H:i', strtotime($m['data_envio'])); ?></small>
            </div>
        <?php endwhile; ?>
        

        <form action="enviar_mensagem.php" method="POST">
            <input type="hidden" name="destinatario_id" value="<?php echo $destinatario_id; ?>">
            <textarea name="mensagem" required placeholder="Digite sua mensagem..."></textarea>
        
            <button type="submit">Enviar</button>
            <?php
    $linkVoltar = ($tipo == 'cliente') ? '../cliente/painel_cliente.php' : '../funcionario/painel_funcionario.php';
    ?>
<a href="<?php echo $linkVoltar; ?>">
    <button type="button">Voltar para o Painel</button>
</a>  
        </form>
        <?php if ($_SESSION['tipo'] === 'cliente'): ?>
    <form action="../php/contratar.php" method="POST">
        <input type="hidden" name="funcionario_id" value="<?= htmlspecialchars($destinatario_id) ?>">
        <button type="submit">Contratar</button>
    </form>
<?php endif; ?>
    </div>
</body>
</html>
