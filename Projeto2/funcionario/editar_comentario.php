<?php
session_start();
include("../php/conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comentario_id'], $_POST['comentario'])) {
    $comentario_id = (int)$_POST['comentario_id'];
    $novo_comentario = trim($_POST['comentario']);
    $id_usuario = $_SESSION['id'];

    // Verifica se o comentário pertence ao usuário
    $verifica = $conn->prepare("SELECT id FROM comentarios_servico WHERE id = ? AND id_usuario = ?");
    $verifica->bind_param("ii", $comentario_id, $id_usuario);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0 && !empty($novo_comentario)) {
        $stmt = $conn->prepare("UPDATE comentarios_servico SET comentario = ? WHERE id = ?");
        $stmt->bind_param("si", $novo_comentario, $comentario_id);
        $stmt->execute();
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
