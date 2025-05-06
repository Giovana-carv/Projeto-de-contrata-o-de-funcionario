<?php
session_start();
if ($_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
$id_func = $_SESSION['id'];

$sql = "SELECT c.id AS contrato_id, u.nome, u.foto_perfil, u.endereco
        FROM contratacoes c
        JOIN usuarios u ON c.cliente_id = u.id
        WHERE c.funcionario_id = $id_func AND c.status = 'pendente'";
$res = $conn->query($sql);

// Adicione este bloco antes do HTML, caso não use sessão para armazenar a imagem:
$sqlFoto = "SELECT foto_perfil FROM usuarios WHERE id = $id_func";
$resFoto = $conn->query($sqlFoto);
$foto = $resFoto->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Funcionário</title>
    <style>
        *{
    margin: 0;
}

/*body{
    background-color: #000;
}*/

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
            /*transition: all 200ms ease-in;*/
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

        .solicitacao {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
            width: 300px;
        }
        img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body>
        <nav class="nav">
        <div class="logo">
            <img src="../uploads/<?php echo $foto['foto_perfil'];?>" alt="foto de perfil">
            <h1>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h1>
        </div>
        <div class="links">
            <a href="../php/solicitacoes.php">Solicitações de Contratação</a>
            <a href="../php/editar_perfil.php"> Editar Perfil </a>
            <a href=""> Sobre nos</a>
            <a class="login" href="../php/logout.php">Sair</a>
        </div>
    </nav>

    <h2> Histórico de Serviços </h2>
</body>
</html>