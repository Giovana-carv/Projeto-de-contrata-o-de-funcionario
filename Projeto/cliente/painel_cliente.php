<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastrov.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$cliente_id = $_SESSION['id'];

// Notificações
$stmt = $conn->prepare("SELECT c.id, c.status, c.notificacao_cliente, u.nome AS nome_funcionario
                        FROM contratacoes c
                        JOIN usuarios u ON c.id_funcionario = u.id
                        WHERE c.id_cliente = ? AND c.notificacao_cliente != ''");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$res = $stmt->get_result();

// Foto do cliente
$stmtFoto = $conn->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
$stmtFoto->bind_param("i", $cliente_id);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = $resFoto->fetch_assoc();

// Lista de funcionários
$funcionarios = $conn->query("SELECT * FROM usuarios WHERE tipo='funcionario'");

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Cliente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #fffaf3;
            color: #212121;
        }

        a {
            text-decoration: none;
            color: #fff;
            transition: color 0.2s ease;
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: #ff416c;
            color: #fff;
        }

        .nav .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: bold;
        }

        .nav .logo img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .nav .links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-weight: bold;
        }

        .nav .links a {
            font-size: 1.1rem;
            position: relative;
            color: white;
        }

        .nav .links a:not(.login)::before {
            content: "";
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #fff;
            transition: width 0.2s ease;
        }

        .nav .links a:hover::before {
            width: 100%;
        }

        .nav .links a.login {
            background-color: #fff;
            color: black;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: bold;
        }

        .nav .links a.login:hover {
            background-color:rgb(187, 7, 49);
            color: #fff;
        }

        h2, h3 {
            padding: 1rem;
            text-align: center;
            color: black;
        }

        .solicitacoes-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 1rem;
        }

        .card {
            border: 1px solid #ffffff;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px #ff416c;
            padding: 25px;
            width: 250px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 80px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
        }

        .estrela {
            color: #ff9800;
            font-size: 20px;
            margin: 0 2px;
        }

        form {
            margin-top: 10px;
        }

        select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 8px;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            background-color:rgb(185, 40, 74);
            color: #fff;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: rgb(170, 36, 67);
        }

        @media(max-width: 600px) {
            .nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav .links {
                justify-content: flex-start;
                margin-top: 10px;
            }

            h2 {
                font-size: 1.3rem;
            }

            .card {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="logo">
            <?php if (!empty($foto['foto_perfil'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($foto['foto_perfil']); ?>" alt="foto de perfil">
            <?php endif; ?>
            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
        </div>

        <div class="links">
            <a href="../php/top10.php">Top 10 Funcionários</a>
            <a href="../php/editar_perfil.php">Editar Perfil</a>
            <a href="../php/pesquisar.php">Pesquisar</a>
            <a href="notificacoes.php">Notificações</a>
            <a href="../php/dashboard.php">
        Dashboard
    </a>
            <a class="login" href="../php/logout.php">Sair</a>
        </div>
    </nav>

    <h3>Lista de Funcionários</h3>    

    <div class="solicitacoes-container">

    <?php if ($funcionarios->num_rows > 0): ?>
        <?php while($f = $funcionarios->fetch_assoc()): ?>
    <div class="card">
        <?php if (!empty($f['foto_perfil'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($f['foto_perfil']); ?>" alt="Foto do Funcionário">
        <?php endif; ?>

        <?php if (!empty($f['certificado'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($f['certificado']); ?>" alt="Certificado">
        <?php endif; ?>

        <strong><?php echo htmlspecialchars($f['nome']); ?></strong><br>
        <p><?php echo htmlspecialchars($f['ocupacao']); ?></p> <!-- Ocupação aqui -->

        <p class="estrela">Avaliação Média:
            <?php
                $estrelas = round($f['media_avaliacao']);
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $estrelas ? "★" : "☆";
                }
            ?>
        </p>
        
        <form action="../php/avaliar.php" method="POST">
            <input type="hidden" name="id_funcionario" value="<?= htmlspecialchars($f['id']) ?>">
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
    <input type="hidden" name="funcionario_id" value="<?= htmlspecialchars($f['id']) ?>">
    <button type="submit">Contratar</button>
</form>

<a href="../chat/chat.php?destinatario_id=<?= $f['id'] ?>">
    <button>Conversar</button>
</a>  
    </div>
<?php endwhile; ?>

    <?php else: ?>
        <p>Nenhum funcionário disponível no momento.</p>
    <?php endif; ?>
    </div>

</body>
</html>
