<?php
include ('conexao.php');
session_start();

$busca = $_GET['ocupacao'] ?? '';

$sql = "SELECT * FROM usuarios WHERE tipo = 'funcionario' AND ocupacao LIKE ?";
$stmt = $conn->prepare($sql);
$termo = "%" . $busca . "%";
$stmt->bind_param("s", $termo);
$stmt->execute();
$result = $stmt->get_result();


//$funcionarios = $conn->query("SELECT * FROM usuarios WHERE tipo='funcionario'");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar</title>
    <style>
        .card{
            border: 1px solid #ccc;
            border-radius: 10px;
            margin: 15px;
            padding: 10px;
            width: 250px;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }
        img{
            max-width: 90%;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .estrela{
            color: gold;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <form method="GET">
    <input type="text" name="ocupacao" placeholder="Pesquisar por ocupação..." value="<?php echo htmlspecialchars($busca); ?>">
    <button type="submit">Buscar</button>
    <a href="../cliente/painel_cliente.php"> Voltar </a>
</form>
<?php /* while ($row = $result->fetch_assoc()) { ?>
     <div class="card">
        <img src="../uploads/<?php echo $f['foto_perfil'];?>" alt="foto de perfil"><br>
        <img src="../uploads/<?php echo $f['certificado'];?>" alt="certificado"><br> */ ?>
<?php
        //while($f = $funcionarios->fetch_assoc()){?>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="card">
        <img src="../uploads/<?php echo $row['foto_perfil'];?>" alt="foto de perfil"><br>
        <img src="../uploads/<?php echo $row['certificado'];?>" alt="certificado"><br>
        <strong><?php echo $row['nome']; ?></strong><br>
        <p> Avaliação Média: <?php $estrelas = round($row['media_avaliacao']);
        for($i = 1; $i <= 5; $i++){
            echo $i <= $estrelas ? "★" : "☆";
                    }
                ?>
            </p>
            <form action="../php/avaliar.php" method="POST">
                <input type="hidden" name="funcionario_id" value="<?= $f['id'] ?>">
                Avaliar:
                <select name="estrelas">
                    <option value="1">1 estrela</option>
                    <option value="2">2 estrelas</option>
                    <option value="3">3 estrelas</option>
                    <option value="4">4 estrelas</option>
                    <option value="5">5 estrelas</option>
                </select>
                <button type="submit">Enviar</button>
            </form>
            <form action="../php/contratar.php" method="POST">
                <input type="hidden" name="funcionario_id" value="<?= $row['id'] ?>">
                <button type="submit">Contratar</button>
            </form>
        </div> <?php } ?>

    </div> 
</body>
</html>

