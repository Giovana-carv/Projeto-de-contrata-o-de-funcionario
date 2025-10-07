<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['id_gerente'])) {
    exit("Acesso negado");
}

$id_usuario = intval($_POST['id_usuario']);
$id_denuncia = intval($_POST['id_denuncia']);
$tempo = intval($_POST['tempo']); // dias
$id_gerente = $_SESSION['id_gerente'];

// data fim do ban
if ($tempo > 0) {
    $data_fim = date('Y-m-d H:i:s', strtotime("+$tempo days"));
} else {
    $data_fim = null; // ban permanente
}

// registra banimento
$stmt = $conn->prepare("INSERT INTO banimentos (id_usuario, id_gerente, data_fim, motivo) 
                        VALUES (?, ?, ?, 'Banimento por denúncia')");
$stmt->bind_param("iis", $id_usuario, $id_gerente, $data_fim);
$stmt->execute();

// atualiza status da denúncia
$conn->query("UPDATE denuncias SET status = 'analisada' WHERE id_denuncia = $id_denuncia");

header("Location: painelDenuncias.php");
exit;

// após executar o banimento com sucesso:
if ($stmt->execute()) {
    // registrar ação no histórico
    $acao = "banimento";
    $detalhes = "Usuário banido por {$tempo} dia(s)";
    $log = $conn->prepare("INSERT INTO historico_acoes_gerente (id_gerente, id_denuncia, acao, detalhes) VALUES (?, ?, ?, ?)");
    $log->bind_param("iiss", $_SESSION['id_gerente'], $id_denuncia, $acao, $detalhes);
    $log->execute();

    echo "<script>alert('Usuário banido com sucesso!'); window.location.href='painelDenuncias.php';</script>";
}
else {
    echo "<script>alert('Erro ao banir usuário.'); window.location.href='painelDenuncias.php';</script>";
}