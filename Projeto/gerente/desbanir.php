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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banimento</title>
</head>
<body>
    <form method="POST">
        <label for="email"> E-mail do usuário: </label>
        <input type="email" id="email" name="email" required><br>

        <input type="submit" value="Desbanir Usuario">
    </form>
</body>
</html>