<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
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
        }

        body {
            font-family: Arial, sans-serif;
        }

        a {
            text-decoration: none;
            color: #fff;
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: #fff;
            background-color: #212121;
        }

        .nav .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
        }

        .nav .logo img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }

        .nav .links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            font-weight: bold;
        }

        .nav .links a {
            color: rgb(199, 199, 199);
            font-size: 1.1rem;
            margin: 0 0.5rem;
            position: relative;
            transition: all 200ms ease-in;
        }

        .nav .links a:not(.login)::before {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 0.1rem;
            background-color: #fff;
            transition: all 200ms ease-in;
        }

        .nav .links a:hover::before {
            width: 100%;
        }

        .nav .links a.login {
            background-color: rgb(255, 255, 255);
            color: #212121;
            padding: 0.5rem 1.5rem;
            border-radius: 1.5rem;
        }

        .nav .links a.login:hover {
            color: #fff;
            background-color: #000;
        }

        h2, h3 {
            padding: 1rem;
            text-align: center;
        }

        .solicitacoes-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            margin: 15px;
            padding: 10px;
            width: 250px;
            text-align: center;
            background-color: #f9f9f9;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .card img {
            width: 79px;/*Mudar esse valor para alterar o tamanho */
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .estrela {
            color: gold;
            font-size: 20px;
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
                <p>Avaliação Média:
                    <?php
                        $estrelas = round($f['media_avaliacao']);
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $estrelas ? "★" : "☆";
                        }
                    ?>
                </p>

                <form action="../php/avaliar.php" method="POST">
                    <input type="hidden" name="funcionario_id" value="<?= htmlspecialchars($f['id']) ?>">
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
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum funcionário disponível no momento.</p>
    <?php endif; ?>
    </div>

    <h2>🔔 Respostas dos Pedidos</h2>
<?php
$stmt = $conn->prepare("SELECT c.*, u.nome AS nome_funcionario 
                        FROM contratacoes c
                        JOIN usuarios u ON c.id_funcionario = u.id
                        WHERE c.id_cliente = ? AND c.notificacao_cliente = TRUE");
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                Funcionário: <strong><?= htmlspecialchars($row['nome_funcionario']) ?></strong><br>
                Resposta: <strong><?= ucfirst($row['acao']) ?></strong><br>
                Data: <?= $row['data_solicitacao'] ?>
                <hr>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>Nenhuma nova resposta.</p>
<?php endif; ?>

</body>
</html>
