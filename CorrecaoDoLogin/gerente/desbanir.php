<?php
include("../php/conexao.php");

if (isset($_POST['id_banimento'])) {
    $id_banimento = $_POST['id_banimento'];

    // opção 1: apagar o registro
    // $sql = "DELETE FROM banimentos WHERE id_banimento = ?";

    // opção 2: encerrar banimento (mais seguro, mantém histórico)
    $sql = "UPDATE banimentos SET data_fim = NOW() WHERE id_banimento = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_banimento);

    if ($stmt->execute()) {
        echo "Usuário desbanido com sucesso! <a href='painelDenuncias.php'>Voltar</a>";
    } else {
        echo "Erro ao desbanir.";
    }
}
?>
