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
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 20px;
            background-color: #f9f9f9;
        }
        .card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #aaa;
        }
        .info {
            flex: 1;
        }
    
        
        .btn-perfil {
            right: 20%;
            position:absolute;
            display: flex;
            padding: 5px 10px;
            background-color: #ff416c;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .btn-perfil:hover {
            background-color: rgb(170, 36, 67);
        }

        .voltar{
            border-radius: 20px;
            border: 1px solid #ff416c;
            background-color: #ff416c;
            letter-spacing: 1px;
            cursor: pointer;
        }
        .voltar:hover{
            background-color: rgb(170, 36, 67);
        }
        .voltar a{
        text-decoration: none;
        color: black;
        }
    </style>
</head>
<body>
    <form method="GET">
    <input type="text" name="ocupacao" placeholder="Pesquisar por ocupação..." value="<?php echo htmlspecialchars($busca); ?>">
    <button type="submit" class="voltar">Buscar</button>
   <button type="submit" class="voltar"><a href="../cliente/painel_cliente.php"> Voltar </a></button>
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
        <div class="info">
        <strong><?php echo $row['nome']; ?></strong><br>
       <!-- <a class="btn-perfil" href="perfil_funcionario.php?id=<?php//php echo $row['id']; ?>">Ver Perfil</a>-->

        <p> Avaliação Média: <?php $estrelas = round($row['media_avaliacao']);
        for($i = 1; $i <= 5; $i++){
            echo $i <= $estrelas ? "★" : "☆";
                    }
                ?>
            </p>
          <!--  <form action="../php/avaliar.php" method="POST">
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
            </form>-->
            <form action="../php/contratar.php" method="POST">
                <input type="hidden" name="funcionario_id" value="<?= $row['id'] ?>">
                <button type="submit">Contratar</button>
            </form>
        </div>
        </div> <?php } ?>
    </div> 
</body>
</html>

