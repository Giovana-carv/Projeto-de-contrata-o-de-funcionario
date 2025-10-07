<?php
session_start();
include("../php/conexao.php");

// Apenas gerentes podem acessar
if (!isset($_SESSION['id_gerente'])) {
    exit("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_denuncia'])) {
    $id_denuncia = intval($_POST['id_denuncia']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_denuncia'])) {
    $id_denuncia = intval($_POST['id_denuncia']);

    // registrar ação antes da exclusão
    $acao = "exclusao";
    $detalhes = "Denúncia excluída do sistema.";
    $log = $conn->prepare("INSERT INTO historico_acoes_gerente (id_gerente, id_denuncia, acao, detalhes) VALUES (?, ?, ?, ?)");
    $log->bind_param("iiss", $_SESSION['id_gerente'], $id_denuncia, $acao, $detalhes);
    $log->execute();

    // Deleta apenas a denúncia, sem afetar banimentos
    $stmt = $conn->prepare("DELETE FROM denuncias WHERE id_denuncia = ?");
    $stmt->bind_param("i", $id_denuncia);

    if ($stmt->execute()) {
        echo "<script>alert('Denúncia excluída com sucesso!'); window.location.href='painelDenuncias.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir denúncia.'); window.location.href='painelDenuncias.php';</script>";
    }
    }
}