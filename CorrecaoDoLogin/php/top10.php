<?php
include('conexao.php');
session_start();

// Consulta para os 10 funcionários mais bem avaliados
$sql = "SELECT u.id, u.nome, u.ocupacao, u.foto_perfil,/*Seleciona os dados básicos dos funcionários: ID, nome, ocupação (ex: encanador, pintor), e a foto de perfil.*/
    AVG(a.estrelas) as media/*Calcula a média das estrelas recebidas por cada funcionário, a partir da tabela de avaliações.*/
    FROM usuarios u /*Os dados dos funcionários estão na tabela usuarios, que foi apelidada como u.*/
    JOIN avaliacoes a ON u.id = a.id_funcionario /*Faz uma junção interna (INNER JOIN) entre a tabela usuarios e avaliacoes, ligando o id do usuário com o campo id_funcionario nas avaliações.Ou seja, pega as avaliações feitas para os usuários do tipo funcionário*/
    WHERE u.tipo = 'funcionario' /*Filtra apenas os usuários que são do tipo funcionario, excluindo clientes, gerentes, etc.*/
    GROUP BY u.id, u.nome, u.ocupacao, u.foto_perfil /*Agrupa os resultados por funcionário, já que estamos usando AVG() (uma função agregada).
Isso garante que cada linha do resultado seja referente a um único funcionário, com sua média de avaliações.*/
    ORDER BY media DESC /*Ordena os funcionários da maior para a menor média de estrelas.*/
    LIMIT 10"; //limite de 10

$result = $conn->query($sql);//Usa o objeto $conn para executar a consulta SQL.
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
            cursor: pointer;
        }
        .btn-perfil:hover {
            background-color: rgb(170, 36, 67);
        }
    .theme-toggle {
      background:rgba(255,255,255,0.2);
      border-radius:50%;
      width:40px;
      height:40px;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .theme-toggle:hover {
      background:rgba(255,255,255,0.35);
      cursor: pointer;
    }
    body.dark {
      background:#1e1e1e;
      color: black;
    }
    body h2{
        color: black;
        text-align: center;
    }
    body.dark h2{
        color: white;
        text-align: center;
    }
    body.dark .card {
      background:#2a2a2a;
      color:#ddd;
    }
    </style>
</head>
<body>
    <button class="theme-toggle" id="theme-toggle">🌙</button>

    <h2>Top 10 Funcionários Mais Bem Avaliados</h2>

 <form action='../cliente/painel_cliente.php' method='get'>
        <button type='submit' class="btn-perfil">Voltar</button>
      </form>
      <?php 
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
                    <a class="btn-perfil" href="perfil_funcionario.php?id=<?php echo $row['id']; ?>">Ver Perfil</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum funcionário avaliado ainda.</p>
    <?php endif;  ?>

    <script src="../js/tema.js"></script>
</body>
</html>