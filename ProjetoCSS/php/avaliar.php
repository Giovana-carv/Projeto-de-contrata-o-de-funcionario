<?php
session_start();
include("conexao.php");

$cliente_id = $_SESSION['id'];
$id_funcionario = $_POST['id_funcionario'];
$estrelas = $_POST['estrelas'];

// Inserir avaliação
$conn->query("INSERT INTO avaliacoes (id_cliente, id_funcionario, estrelas)
              VALUES ('$cliente_id', '$id_funcionario', '$estrelas')");

// Atualizar média
$media = $conn->query("SELECT AVG(estrelas) AS media FROM avaliacoes WHERE id_funcionario = $id_funcionario")->fetch_assoc()['media'];
$conn->query("UPDATE usuarios SET media_avaliacao = '$media' WHERE id = $id_funcionario");

// após processar a avaliação...
$_SESSION['mensagem'] = "Avaliação enviada com sucesso!";
$_SESSION['tipo_mensagem'] = "sucesso";
header("Location: pesquisar.php");

//redireciona
if (!empty($_POST['redirect_to'])) {
    header("Location: " . $_POST['redirect_to']);
} else {
    header("Location: ../cliente/painel_cliente.php");
}

exit;
//$_SESSION['mensagem'] = "Erro ao enviar avaliação.";
//$_SESSION['tipo_mensagem'] = "erro";




//header("Location: ../cliente/painel_cliente.php");
exit;
?>
