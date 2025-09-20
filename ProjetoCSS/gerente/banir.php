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
