<?php
include ('conexao.php');
session_start();
$mensagem = '';
$tipo_mensagem = '';

if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $tipo_mensagem = $_SESSION['tipo_mensagem'] ?? 'sucesso';
    unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']);
}


$busca = $_GET['ocupacao'] ?? '';

$sql = "SELECT * FROM usuarios WHERE tipo = 'funcionario' AND ocupacao LIKE ?";
$stmt = $conn->prepare($sql);
$termo = "%" . $busca . "%";
$stmt->bind_param("s", $termo);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar Funcionários</title>
    <style>
    * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
        }
    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f2f2f2;
    padding: 20px;
    max-width: 900px;
    margin: auto;
        }
    form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
    align-items: center;
    justify-content: center;
        }
    input[type="text"] {
    flex: 1 1 300px;
    padding: 10px 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
        }
    .voltar,
    .voltar a,
    button[type="submit"] {
    background-color: #ff416c;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
    text-decoration: none;
        }
    .voltar:hover,
    button[type="submit"]:hover {
    background-color: #c03557;
        }
    .card {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    gap: 20px;
    padding: 20px;
    margin-bottom: 20px;
    align-items: center;
        }
    .card img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ff416c;
        }
    .info {
    flex: 1;
        }
    .info strong {
    font-size: 20px;
    display: block;
    margin-bottom: 8px;
    color: #333;
        }
    .info p {
    font-size: 16px;
    color: #666;
    margin: 6px 0;
        }
    .info form {
    margin-top: 10px;
        }
    .info form select,
    .info form button {
    padding: 6px 10px;
    font-size: 15px;
    border-radius: 6px;
    border: none;
    margin-top: 5px;
        }
    .info form select {
    background-color: #f0f0f0;
    margin-right: 10px;
        }
    .btn-avaliar {
    background-color: #007BFF;
    color: white;
    transition: 0.3s;
        }
    .btn-avaliar:hover {
    background-color: #0056b3;
        }
    .btn-contratar {
    background-color: #4CAF50;
    color: white;
    transition: 0.3s;
        }
    .btn-contratar:hover {
    background-color: #3e8e41;
        }
    .imagens {
    display: flex;
    flex-direction: column;
    gap: 10px;
        }
    .imagens img:last-child {
    border-radius: 8px;
    width: 100px;  
    height: auto;
    border: 2px dashed #aaa;
        }
    .mensagem {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-size: 16px;
    text-align: center;
    animation: fadeout 3s ease-in-out forwards;
        }
    .mensagem.sucesso {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
        }
    .mensagem.erro {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
        }
    @keyframes fadeout {
    0% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body>

<?php if ($mensagem): ?>
    <div class="mensagem <?= $tipo_mensagem ?>">
        <?= htmlspecialchars($mensagem) ?>
    </div>
<?php endif; ?>

<form method="GET">
    <input type="text" name="ocupacao" placeholder="Pesquisar por ocupação..." value="<?php echo htmlspecialchars($busca); ?>">
    <button type="submit" class="voltar">Buscar</button>
    <a href="../cliente/painel_cliente.php" class="voltar">Voltar</a>
</form>

<?php while ($row = $result->fetch_assoc()) { ?>
    <div class="card">
        <div class="imagens">
            <img src="../uploads/<?php echo $row['foto_perfil']; ?>" alt="Foto de perfil">
            <img src="../uploads/<?php echo $row['certificado']; ?>" alt="Certificado">
        </div>
        <div class="info">
            <strong><?php echo $row['nome']; ?></strong>
            <p>Ocupação: <?php echo htmlspecialchars($row['ocupacao']); ?></p>


            <p>
                Avaliação Média: 
                <?php 
                $estrelas = round($row['media_avaliacao']);
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $estrelas ? "★" : "☆";
                }
                ?>
            </p>

            <form action="avaliar.php" method="POST">
<input type="hidden" name="id_funcionario" value="<?= $row['id'] ?>">
    <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    Avaliar:
    <select name="estrelas" required>
        <option value="1">1 estrela</option>
        <option value="2">2 estrelas</option>
        <option value="3">3 estrelas</option>
        <option value="4">4 estrelas</option>
        <option value="5">5 estrelas</option>
    </select>
    <button type="submit" class="btn-avaliar">Enviar</button>
</form>

<form action="contratar.php" method="POST">
    <input type="hidden" name="funcionario_id" value="<?= $row['id'] ?>">
    <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <button type="submit" class="btn-contratar">Contratar</button>
</form>

        </div>
    </div>
<?php } ?>

</body>
</html>
