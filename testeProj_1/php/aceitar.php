<?php
include("conexao.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE contratacoes 
                            SET status = 'em atendimento', 
                                notificacao_cliente = 'Seu pedido foi aceito pelo funcionário.',
                                notificacao_funcionario = FALSE
                            WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: solicitacoes.php");
exit;

/*
include("conexao.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("UPDATE contratacoes 
                        SET status = 'aceito', 
                            notificacao_cliente = 'Seu pedido foi aceito pelo funcionário.',
                            notificacao_funcionario = FALSE
                        WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: solicitacoes.php");
exit;

/*
<?php
include("conexao.php");
$id = $_GET['id'];

$conn->query("UPDATE contratacoes 
              SET status = 'aceito', 
                  notificacao_cliente = 'Seu pedido foi aceito pelo funcionário.',
                  notificacao_funcionario = FALSE
              WHERE id = $id");

header("Location: solicitacoes.php");
exit;*/
?>
