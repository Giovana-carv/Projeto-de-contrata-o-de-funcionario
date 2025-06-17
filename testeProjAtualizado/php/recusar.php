<?php
include("conexao.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE contratacoes 
                            SET status = 'recusado', 
                                notificacao_cliente = 'Seu pedido foi recusado pelo funcionário.',
                                notificacao_funcionario = FALSE
                            WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: solicitacoes.php");
exit;
/*
include("conexao.php");

// Verifica se o ID foi passado corretamente via GET e é um número
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

// Converte o ID para inteiro
$id = (int) $_GET['id'];

// Prepara e executa a query com segurança
$stmt = $conn->prepare("UPDATE contratacoes 
                        SET status = 'recusado', 
                            notificacao_cliente = 'Seu pedido foi recusado pelo funcionário.',
                            notificacao_funcionario = FALSE
                        WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Redireciona de volta para a página de solicitações
header("Location: solicitacoes.php");
exit;

/*<?php
include("conexao.php");

$id = $_GET['id'];

$conn->query("UPDATE contratacoes 
              SET status = 'recusado', 
                  notificacao_cliente = 'Seu pedido foi recusado pelo funcionário.',
                  notificacao_funcionario = FALSE
              WHERE id = $id");

header("Location: solicitacoes.php");
exit;*/
?>
