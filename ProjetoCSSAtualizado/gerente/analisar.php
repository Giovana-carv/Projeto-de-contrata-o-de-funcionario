<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['id_gerente'])) {
    exit("Acesso negado");
}

$id_denuncia = intval($_POST['id_denuncia']);
$conn->query("UPDATE denuncias SET status = 'analisada' WHERE id_denuncia = $id_denuncia");

header("Location: painelDenuncias.php");
exit;

// depois de marcar como analisada
if ($stmt->execute()) {
    $acao = "analisada";
    $detalhes = "Denúncia revisada sem necessidade de banimento.";
    $log = $conn->prepare("INSERT INTO historico_acoes_gerente (id_gerente, id_denuncia, acao, detalhes) VALUES (?, ?, ?, ?)");
    $log->bind_param("iiss", $_SESSION['id_gerente'], $id_denuncia, $acao, $detalhes);
    $log->execute();

    echo "<script>alert('Denúncia marcada como analisada.'); window.location.href='painelDenuncias.php';</script>";
}
else{
        echo "<script>alert('Erro ao analisar usuário.'); window.location.href='painelDenuncias.php';</script>";
}