<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

$cliente_id = $_SESSION['id'];

$sql = "SELECT c.id, c.status, c.notificacao_cliente, u.nome AS nome_funcionario
        FROM contratacoes c
        JOIN usuarios u ON c.funcionario_id = u.id
        WHERE c.cliente_id = $cliente_id AND c.notificacao_cliente != ''";

$res = $conn->query($sql);

// Adicione este bloco antes do HTML, caso não use sessão para armazenar a imagem:
$sqlFoto = "SELECT foto_perfil FROM usuarios WHERE id = $cliente_id";
$resFoto = $conn->query($sqlFoto);
$foto = $resFoto->fetch_assoc();

echo "<h2>Notificações</h2>";

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo "<p>
                <strong>{$row['nome_funcionario']}:</strong> {$row['notificacao_cliente']}
                <a href='../php/marcar_lida.php?id={$row['id']}'>Marcar como lida</a>
              </p>";
    }
} else {
    echo "Nenhuma notificação.";
}
$funcionarios = $conn->query("SELECT * FROM usuarios WHERE tipo='funcionario'");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente</title>
    <style>
    *{
    margin: 0;
}
a{
    text-decoration: none;
    color: #fff;
}

.nav{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 1.5rem;
    color: #fff;
    background-color: #212121;

    & .links{
        font-weight: bold;

        & a{
            position: relative;
            color: rgb(199, 199, 199);
            font-size: 1.1rem;
            margin: 0 0.5rem;
            overflow: hidden;
            transition: all 200ms ease-in;

            &:not(.login):before{
                content: "";
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 0.1rem;
                background-color: #fff;
                transition: all 200ms ease-in;
            }

            &:hover::before{
                width: 100%;
            }

            & :hover{
                color: #fff;
            }
        }
        & .login{
            background-color: rgb(255, 255, 255);
            color: #212121;
            padding: 0.5rem 1.5rem;
            border-radius: 1.5rem;

            &:hover{
                color: #fff;
                background-color: #000;
            }
        }
    }
    & .logo{
    font-weight: bold;
           }
}
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
        <nav class="nav">
        <div class="logo">
            <img src="../uploads/<?php echo $foto['foto_perfil'];?>" alt="foto de perfil">
            <h1>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h1>
    <p>Essa é sua área de cliente.</p>
        </div>

        <div class="links">
            <a href="#">Top 10 Funcionários</a>
            <a href="../php/editar_perfil.php"> Editar Perfil </a>
            <a href="../php/pesquisar.php"> Pesquisar </a>
            <a class="login" href="../php/logout.php">Sair</a>
        </div>
    </nav>

    <h3> Lista de Funcionários</h3>
    <?php
    while($f = $funcionarios->fetch_assoc()):?>
    <div class="card">
        <img src="../uploads/<?php echo $f['foto_perfil'];?>" alt="foto de perfil"><br>
        <img src="../uploads/<?php echo $f['certificado'];?>" alt="certificado"><br>

        <strong><?php echo $f['nome']; ?></strong><br>
        <p> Avaliação Média: <?php $estrelas = round($f['media_avaliacao']);
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
                <input type="hidden" name="funcionario_id" value="<?= $f['id'] ?>">
                <button type="submit">Contratar</button>
            </form>
        </div>
    <?php endwhile; ?>
    </div>
</body>
</html>