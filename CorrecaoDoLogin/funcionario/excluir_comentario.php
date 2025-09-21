<?php
session_start();
include("../php/conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comentario_id'])) {
    $comentario_id = (int)$_POST['comentario_id'];
    $id_usuario = $_SESSION['id'];

    // Verifica se o comentário pertence ao usuário
    $verifica = $conn->prepare("SELECT id FROM comentarios_servico WHERE id = ? AND id_usuario = ?");
    $verifica->bind_param("ii", $comentario_id, $id_usuario);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM comentarios_servico WHERE id = ?");
        $stmt->bind_param("i", $comentario_id);
        $stmt->execute();
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
