<?php
session_start();
include("../php/conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comentario'], $_POST['id_contratacao'])) {
    $comentario = trim($_POST['comentario']);
    $id_contratacao = (int)$_POST['id_contratacao'];
    $id_usuario = $_SESSION['id'];  // ID do usuário logado

    if (!empty($comentario)) {
        $stmt = $conn->prepare("INSERT INTO comentarios_servico (id_contratacao, id_usuario, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_contratacao, $id_usuario, $comentario);
        $stmt->execute();
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
