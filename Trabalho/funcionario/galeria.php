<?php
session_start();
include("../php/conexao.php");
$id_funcionario = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Galeria de Serviços Finalizados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        .galeria {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .item {
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 250px;
            text-align: center;
        }
        .item img {
            width: 100%;
            border-radius: 8px;
        }
        .info {
            font-size: 14px;
            color: #555;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<h2>Seus Serviços Finalizados</h2>

<div class="galeria">
<?php
$sql = "SELECT fs.caminho, c.id AS id_contratacao, c.data_solicitacao, u.nome AS nome_cliente
    FROM fotos_servicos fs
    INNER JOIN contratacoes c ON fs.id_contratacao = c.id
    INNER JOIN usuarios u ON c.cliente_id = u.id
    WHERE c.funcionario_id = ? AND c.status = 'finalizado'
    ORDER BY c.data_solicitacao DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $imagem = $row['caminho'];
    $id_contratacao = $row['id_contratacao'];
    $data = date("d/m/Y", strtotime($row['data_solicitacao']));
    $nome_cliente = htmlspecialchars($row['nome_cliente']);

    echo "
    <div class='item'>
        <img src='$imagem' alt='Serviço $id_contratacao'>
        <div class='info'>
            Cliente: $nome_cliente<br>
            Data: $data<br>
            Contratação #$id_contratacao
        </div>
    </div>";
}
?>
</div>

</body>
</html>