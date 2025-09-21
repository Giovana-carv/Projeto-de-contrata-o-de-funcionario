<?php
session_start();
include("../php/conexao.php");

if (!isset($_SESSION['id_gerente'])) {
    exit("Acesso negado");
}

$id_denuncia = intval($_POST['id_denuncia']);
$conn->query("UPDATE denuncias SET status = 'analisada' WHERE id_denuncia = $id_denuncia");

header("Location: painelDenunciasv.php");
exit;
