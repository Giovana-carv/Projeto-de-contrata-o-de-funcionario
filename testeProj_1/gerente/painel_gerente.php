<?php
session_start();

if(!isset($_SESSION['gerente_id'])){
    header("Location: login_gerente.php");
    exit;
}
include "../php/conexao.php";

// Foto do gerente
$stmtFoto = $conn->prepare("SELECT foto_perfil FROM gerente WHERE id = ?");
$stmtFoto->bind_param("i", $id);
$stmtFoto->execute();
$resFoto = $stmtFoto->get_result();
$foto = $resFoto->fetch_assoc();

// Lista de funcionários
$funcionarios = $conn->query("SELECT * FROM usuarios WHERE tipo='funcionario'");

// Lista de clientes
$funcionarios = $conn->query("SELECT * FROM usuarios WHERE tipo='cliente'");

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
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
            <a href="../php/editar_perfil.php">Editar Perfil</a>
            <a href="../php/pesquisar.php">Pesquisar</a>
            <a href="notificacoes_gerente.php">Notificações</a>
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
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum funcionário disponível no momento.</p>
    <?php endif; ?>
    </div>
</body>
</html>