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
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']); //novo
    $ocupacao = isset($_POST['ocupacao']) ? mysqli_real_escape_string($conn, $_POST['ocupacao']) : '';
    $comentario = isset($_POST['comentario']) ? mysqli_real_escape_string($conn, $_POST['comentario']) : '';

    $atualizacoes = "nome = '$nome', endereco = '$endereco', email = '$email', senha = '$senha', telefone = '$telefone'"; //novo
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
    <link rel="stylesheet" href="../css/temaEScuro.css">
    <title>Editar Perfil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        section {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 12px #ff416c;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .inputbox {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #4a90e2;
        }
        button {
            padding: 12px;
            background-color: rgb(185, 40, 74);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color:rgb(170, 36, 67);
        }
        a button {
            background-color:rgb(170, 36, 67);
        }
        a button:hover {
            background-color:rgb(170, 36, 67);
        }
        a {
            text-align: center;
            text-decoration: none;
            display: block;
        }
    </style>
</head>
<body>
<section>
    <button class="theme-toggle" id="theme-toggle">🌙</button>

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
    <label> Telefone: </label>
<input type="tel" oninput="formatarTelefone(this)" onblur="corrigirFormatoFinal(this)" name="telefone" value="<?php echo $dados['telefone']; ?>"><br>

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

<script src="../js/tema.js"></script>

<script>
function formatarTelefone(campo) {
    let numero = campo.value.replace(/\D/g, '');

    // Limita o número a no máximo 11 dígitos
    if (numero.length > 11) numero = numero.slice(0, 11);

    if (numero.length <= 10) {
        // (99) 9999-9999 (parcial ou fixo)
        campo.value = numero.replace(/(\d{0,2})(\d{0,4})(\d{0,4})/, function(_, ddd, parte1, parte2) {
            let resultado = '';
            if (ddd) resultado += '(' + ddd;
            if (parte1) resultado += ') ' + parte1;
            if (parte2) resultado += '-' + parte2;
            return resultado;
        });
    } else {
        // (99) 99999-9999 (celular)
        campo.value = numero.replace(/(\d{0,2})(\d{0,5})(\d{0,4})/, function(_, ddd, parte1, parte2) {
            let resultado = '';
            if (ddd) resultado += '(' + ddd;
            if (parte1) resultado += ') ' + parte1;
            if (parte2) resultado += '-' + parte2;
            return resultado;
        });
    }
}

function corrigirFormatoFinal(campo) {
    // Garante a formatação final ao perder o foco
    let numero = campo.value.replace(/\D/g, '');

    if (numero.length === 11) {
        campo.value = numero.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (numero.length === 10) {
        campo.value = numero.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        campo.value = numero; // Caso incompleto
    }
}
</script>


</body>
</html>