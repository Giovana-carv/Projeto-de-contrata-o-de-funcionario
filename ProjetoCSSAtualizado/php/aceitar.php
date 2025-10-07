<?php
include("conexao.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE contratacoes 
        SET status = 'em atendimento', 
        acao = 'aceito',
        notificacao_cliente = 'Seu pedido foi aceito pelo funcionário.',
        notificacao_funcionario = NULL 
        WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: solicitacoes.php");
exit;
?>
