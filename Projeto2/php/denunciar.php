<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id'])) {
    echo "Acesso negado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_denunciante = $_SESSION['id'];
    $id_denunciado = intval($_POST['denunciado_id']);
    $motivo = trim($_POST['motivo']);

    if ($id_denunciante === $id_denunciado) {
        echo "<script>alert('Você não pode se denunciar.'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO denuncias (id_denunciante, id_denunciado, motivo) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_denunciante, $id_denunciado, $motivo);

    if ($stmt->execute()) {
        echo "<script>alert('Denúncia enviada ao gerente!'); window.location.href='../chat/chat.php?destinatario_id={$id_denunciado}';</script>";
    } else {
        echo "Erro ao registrar denúncia: " . $conn->error;
    }
}
?>
