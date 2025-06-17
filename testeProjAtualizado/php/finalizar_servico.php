<?php
include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_contratacao = (int)$_POST['id_contratacao'];
    $foto_nome = $_FILES['foto_servico']['name'];
    $foto_tmp = $_FILES['foto_servico']['tmp_name'];

    if (!empty($foto_nome)) {
        $destino = "../uploads/" . basename($foto_nome);
        move_uploaded_file($foto_tmp, $destino);

        $stmt = $conn->prepare("UPDATE contratacoes SET status = 'finalizado', foto_servico = ? WHERE id = ?");
        $stmt->bind_param("si", $foto_nome, $id_contratacao);
        $stmt->execute();

        header("Location: servicos_antigos.php");
        exit;
    } else {
        echo "Erro: foto não enviada.";
    }
}
?>