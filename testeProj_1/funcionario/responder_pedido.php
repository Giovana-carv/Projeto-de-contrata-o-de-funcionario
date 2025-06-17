<?php
require_once '../php/conexao.php';

session_start();

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$id_funcionario = $_SESSION["usuario_id"];
$id_pedido = $_POST['id_pedido'];
$acao = $_POST['acao'];

if (!in_array($acao, ['aceito', 'recusado'])) {
    echo "Ação inválida.";
    exit;
}

// Atualiza status e notificação
$stmt = $conn->prepare("UPDATE contratacoes 
                        SET acao = ?, notificacao_funcionario = FALSE, notificacao_cliente = TRUE 
                        WHERE id = ? AND id_funcionario = ?");
$stmt->bind_param("sii", $acao, $id_pedido, $id_funcionario);
$stmt->execute();

echo "Pedido " . ($acao === 'aceito' ? 'aceito' : 'recusado') . " com sucesso!";
header("refresh:2;url=painel.php");
