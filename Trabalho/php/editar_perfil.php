<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];
$tipo = $_SESSION['tipo'];
$usuarios = ($tipo == 'cliente') ? 'cliente' : 'funcionario';

$sql = "SELECT * FROM usuarios WHERE id = $id";
$resultado = mysqli_query($conn, $sql);
$dados = mysqli_fetch_assoc($resultado);

if (isset($_POST['salvar'])) {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $endereco = mysqli_real_escape_string($conn, $_POST['endereco']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = mysqli_real_escape_string($conn, $_POST['senha']);
    $ocupacao = isset($_POST['ocupacao']) ? mysqli_real_escape_string($conn, $_POST['ocupacao']) : '';
    $comentario = isset($_POST['comentario']) ? mysqli_real_escape_string($conn, $_POST['comentario']) : '';

    $atualizacoes = "nome = '$nome', endereco = '$endereco', email = '$email', senha = '$senha'";
    if ($tipo == 'funcionario') {
        $atualizacoes .= ", ocupacao = '$ocupacao', comentario = '$comentario'";
    }

    if (!empty($_FILES['foto']['name'])) {
        $nomeFoto = uniqid() . "_" . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/$nomeFoto");
        $atualizacoes .= ", foto = '$nomeFoto'";
    }

    if ($tipo == 'funcionario' && !empty($_FILES['certificado']['name'])) {
        $nomeCertificado = uniqid() . "_" . $_FILES['certificado']['name'];
        move_uploaded_file($_FILES['certificado']['tmp_name'], "../uploads/$nomeCertificado");
        $atualizacoes .= ", certificado = '$nomeCertificado'";
    }

    $sqlUpdate = "UPDATE usuarios SET $atualizacoes WHERE id = $id";
$linkVoltar = ($tipo == 'cliente') ? '../cliente/painel_cliente.php' : '../funcionario/painel_funcionario.php';

    if (mysqli_query($conn, $sqlUpdate)) {
        echo "<script>alert('Perfil atualizado com sucesso!');</script>";
         echo "<a href='$linkVoltar'><button> Voltar </button> </a>";
        exit();
    } else {
        echo "Erro ao atualizar: " . mysqli_error($conn);
            echo "<a href='$linkVoltar'><button> Voltar </button> </a>";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <style>
        body{
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: brown;
        }
        h2{
            font-size: 2rem;
            color: #efff;
            text-align: center;
            letter-spacing: 1px;
            text-shadow: 3px 3px 1px black;
            border-bottom: 2px solid #efff;
            
        }

        section{
            position: relative;
            max-width: 400px;
            background-color: transparent;
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            backdrop-filter: blur(55px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 3rem;
            border-bottom: 2px solid #efff;
        }
        .inputbox{
            position: relative;
            margin: 30px 0;
            max-width: 310px;
            
        }
        .inputbox input{
            width: 100%;
            height: 60px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 1rem;
            padding: 0 35px 0 35px;
            color: #efff;
            
        }

        button{
            width: 100%;
            height: 40px;
            border-radius: 40px;
            background-color: rgba(255, 255, 255, 1);
            outline: none;
            cursor:pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.4s ease;
        }
        button:hover{
            background-color: rgba(255, 255, 255, 0.5);
        }

        label{
            color: #efff;
            border-bottom: 2px solid #efff;
        }
    </style>
</head>
<body>
<section>
<form method="POST" enctype="multipart/form-data">
    <h2>Editar Perfil - <?php echo ucfirst($tipo); ?></h2>
    <div class="inputbox">
    <label> Nome: </label>
    <input type="text" name="nome" value="<?php echo $dados['nome']; ?>"><br>
    <label> Endereço:</label>
    <input type="text" name="endereco" placeholder="Endereço" value="<?php echo $dados['endereco']; ?>"><br>
    <label> Senha:</label>
    <input type="password" name="senha" placeholder="Senha" value="<?php echo $dados['senha']; ?>"><br>
    <label> E-mail:</label>
    <input type="email" name="email" placeholder="Email" value="<?php echo $dados['email']; ?>"><br>

    <?php if ($tipo == 'funcionario'): ?>
        <label> Ocupação:</label>
        <input type="text" name="ocupacao" placeholder="Ocupação" value="<?php echo $dados['ocupacao']; ?>"><br>
        <label> Comentário:</label>
        <input type="text" name="comentario" placeholder="Comentário" value="<?php echo $dados['comentario']; ?>"><br>
    <?php endif; ?>

    <label>Nova foto de perfil:</label>
    <input type="file" name="foto"><br>

    <?php if ($tipo == 'funcionario'): ?>
        <label>Nova foto do certificado:</label>
        <input type="file" name="certificado"><br>
    <?php endif; ?>

    <button type="submit" name="salvar">Salvar</button>
<?php
    $linkVoltar = ($tipo == 'cliente') ? '../cliente/painel_cliente.php' : '../funcionario/painel_funcionario.php';
?>
<a href="<?php echo $linkVoltar; ?>">
    <button type="button">Voltar para o Painel</button>
</a>
    </div>
</form>
</section>
</body>
</html>