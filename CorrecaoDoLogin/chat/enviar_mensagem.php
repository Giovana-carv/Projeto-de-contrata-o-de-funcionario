<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['id']) || !isset($_POST['mensagem']) || !isset($_POST['destinatario_id'])) {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$remetente_id = $_SESSION['id'];
$destinatario_id = (int)$_POST['destinatario_id'];
$mensagem = trim($_POST['mensagem']);

if ($mensagem !== '') {
    $stmt = $conn->prepare("INSERT INTO mensagens (remetente_id, destinatario_id, mensagem) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $remetente_id, $destinatario_id, $mensagem);
    $stmt->execute();
}

header("Location: chat.php?destinatario_id=$destinatario_id");
exit;
