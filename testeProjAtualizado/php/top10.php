<?php
include('conexao.php');
session_start();

// Consulta para os 10 funcionários mais bem avaliados
$sql = "SELECT u.id, u.nome, u.ocupacao, u.foto_perfil, 
    AVG(a.estrelas) as media
    FROM usuarios u
    JOIN avaliacoes a ON u.id = a.id_funcionario
    WHERE u.tipo = 'funcionario'
    GROUP BY u.id, u.nome, u.ocupacao, u.foto_perfil
    ORDER BY media DESC
    LIMIT 10
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Top 10 Funcionários</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
        }
        .funcionario {
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 12px #ff416c;
        }
        .funcionario img {
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
            padding: 5px 10px;
            background-color: rgb(185, 40, 74);
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .btn-perfil:hover {
            background-color: rgb(170, 36, 67);
        }
    </style>
</head>
<body>
    <h2>Top 10 Funcionários Mais Bem Avaliados</h2>

    <?php 
    // Botão seguro para voltar ao painel
echo "<form action='../cliente/painel_cliente.php' method='get'>
        <button type='submit'>Voltar</button>
      </form>";
    if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="funcionario">
                <img src="../uploads/<?php echo $row['foto_perfil']; ?>" alt="Foto do Funcionário">
                <div class="info">
                    <strong><?php echo htmlspecialchars($row['nome']); ?></strong><br>
                    Ocupação: <?php echo htmlspecialchars($row['ocupacao']); ?><br>
                    Média: 
                    <?php
                        $media = round($row['media'], 1);
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $media ? '★' : '☆';
                        }
                        echo " ($media)";
                    ?><br><br>
                   <!-- <a class="btn-perfil" href="perfil_funcionario.php?id=<?php echo $row['id']; ?>">Ver Perfil</a>-->
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum funcionário avaliado ainda.</p>
    <?php endif;  ?>
</body>
</html>