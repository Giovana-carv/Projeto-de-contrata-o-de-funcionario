<?php
session_start();
include("conexao.php");

// Verifica se está logado
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$cliente_id = $_SESSION['id'];
$funcionario_id = $_POST['funcionario_id'];

// Verifica se já existe uma solicitação pendente para esse funcionário
$stmt = $conn->prepare("SELECT id FROM contratacoes WHERE cliente_id = ? AND funcionario_id = ? AND status = 'pendente'");
$stmt->bind_param("ii", $cliente_id, $funcionario_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    // Cria nova solicitação com notificação opcional
    $notificacao = "Você recebeu uma nova solicitação de serviço!";
    $stmt = $conn->prepare("INSERT INTO contratacoes (cliente_id, funcionario_id, status, notificacao_funcionario) VALUES (?, ?, 'pendente', ?)");
    $stmt->bind_param("iis", $cliente_id, $funcionario_id, $notificacao);
    $stmt->execute();
}

header("Location: ../cliente/painel_cliente.php");
exit;
?>
