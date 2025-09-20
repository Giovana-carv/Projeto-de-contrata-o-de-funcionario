<?php
include('conexao.php');
session_start();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_funcionario = (int) $_GET['id'];
} else {
    echo "Funcionário inválido.";
    exit;
}

$id_cliente = $_SESSION['id'];

$sql_fotos = "SELECT u.nome AS nome_funcionario, u.foto_perfil, c.foto_servico, c.data_solicitacao
    FROM contratacoes c
    JOIN usuarios u ON u.id = c.id_funcionario
    WHERE c.id_funcionario = ?
    AND c.foto_servico != ''
    ORDER BY c.data_solicitacao DESC";

$stmt = $conn->prepare($sql_fotos);
if (!$stmt) {
    die("Erro na consulta: " . $conn->error);
}

$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
        }

        h2 {
            text-align: center;
            padding: 20px;
            background-color: #ff416c;
            color: white;
            margin: 0;
        }

        ul {
            list-style: none;
            padding: 0;
            max-width: 900px;
            margin: 40px auto;
        }

        li {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 6px #ff416c;
            padding: 20px;
            margin-bottom: 20px;
            align-items: center;
            padding: 1rem 2rem;
            justify-content: space-between;

        }

        .funcionario {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 12px;
            font-weight: bold;
        }

        .funcionario img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ff416c;
        }

        .info img {
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .info {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 1rem;
        }

        strong {
            color: #17252a;
        }

        hr {
            margin-top: 20px;
            border: none;
            border-top: 1px solid #ccc;
        }

        a {
            display: block;
            text-align: center;
            margin: 30px auto;
            width: fit-content;
            padding: 10px 20px;
            background-color: #ff416c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        a:hover {
            background-color: rgb(187, 7, 49);
        }
    </style>
</head>
<body>
<h2>Serviços Finalizados</h2>
<?php  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // pega a primeira linha
    ?>
    <ul>
        <li>
            <div class="funcionario">
            <img src="../uploads/<?php echo htmlspecialchars($row['foto_perfil']);?>" alt="Foto do Funcionario" style="width: 200px;"><br>
            <strong><?php echo htmlspecialchars($row['nome_funcionario']); ?></strong><br>
            </div> <strong> Último serviço realizado:</strong>
            <hr>

            <div class="info">
            <img src="../uploads/<?php echo htmlspecialchars($row['foto_servico']); ?>" loading="lazy" alt="Foto do serviço" style="width: 200px;"><br>
            <strong>Data da Solicitação:</strong> <?php echo htmlspecialchars($row['data_solicitacao']); ?><br>
            <hr>
        </li>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <li>
                <img src="../uploads/<?php echo htmlspecialchars($row['foto_servico']); ?>" loading="lazy" alt="Foto do serviço" style="width: 200px;"><br>
                <strong>Data da Solicitação:</strong> <?php echo htmlspecialchars($row['data_solicitacao']); ?><br>

                <hr>
        </div>
            </li>
        <?php } ?>
    </ul>

<?php } else { ?>
    <p>Ainda não tem serviços finalizados com fotos.</p>
<?php } ?>

<!--botão “Conversar” apenas se o usuário não for o próprio funcionário-->
<?php if ($_SESSION['id'] != $id_funcionario): ?>
    <a href="../chat/chat.php?destinatario_id=<?= $id_funcionario ?>">Conversar</a>
<?php endif; ?>

<a href='top10.php'>Voltar ao painel</a>

</body>
</html>
