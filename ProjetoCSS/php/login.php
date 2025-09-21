<?php
session_start(); //inicia ou retorna uma sessão, que pode ser definida por um id via GET ou POST
include("conexao.php");

$nome = $_POST['nome'];
$senha = $_POST['senha'];

$mostrar_ban = false;
$data_fim = null;

//verifica se o usuario existe ou se esta banido
$sql = "SELECT * FROM usuarios WHERE nome=? AND senha=?"; //seleciona na tabela 'usuarios' quando 'nome' e 'senha' são usados dados salvos no banco durante cadastro
$stmt = $conn->prepare($sql); //prepare($sql) -> prepara para executar instruções SQL com marcadores de posição
$stmt->bind_param("ss", $nome, $senha); //bind_param("ss", $nome, $senha) -> vincula as variaveis php aos marcadores de posição
$stmt->execute(); //execute -> executa a intrução; stmt -> variavel que armazena o objeto de instrução preparada
$result = $stmt->get_result(); //obtem os resultados

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    // agora vamos verificar na tabela de banimentos
    $id_usuario = $usuario['id'];
    $sql_ban = "SELECT * FROM banimentos 
                WHERE id_usuario = ? 
                ORDER BY id_banimento DESC 
                LIMIT 1"; // pega o último banimento
    $stmt_ban = $conn->prepare($sql_ban);
    $stmt_ban->bind_param("i", $id_usuario);
    $stmt_ban->execute();
    $result_ban = $stmt_ban->get_result();

    if ($result_ban->num_rows > 0) {
        $ban = $result_ban->fetch_assoc();

        $agora = new DateTime();
        $data_fim = !empty($ban['data_fim']) ? new DateTime($ban['data_fim']) : null;//

        // se data_fim for NULL => ban permanente
        if ($data_fim === null || $data_fim > $agora) {
    echo "
    <div class='ban-box'>
        <h2>⚠ Infelizmente sua conta está banida, $nome 😔</h2>";
        
    if ($data_fim) {
        echo "<p>O banimento expira em <strong>" . $data_fim->format('d/m/Y H:i:s') . "</strong></p>";
    } else {
        echo "<p><strong>Este banimento é permanente.</strong></p>";
    }

    echo "<a class='btn-voltar' href='../html/loginCadastrov.html'>Voltar</a>
    </div>";

    exit;
}

    }

    // se não estiver banido, faz login normalmente
    $_SESSION['id'] = $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['tipo'] = $usuario['tipo'];

    if ($usuario['tipo'] === 'cliente') {
        header("Location: ../cliente/painel_cliente.php");
    } else {
        header("Location: ../funcionario/painel_funcionario.php");
    }

} else {
    echo "Nome ou senha inválidos.";
    echo "<a href = '../html/loginCadastrov.html'> Voltar </a>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    .ban-box {
    max-width: 400px;
    margin: 100px auto;
    padding: 20px;
    text-align: center;
    background: #ffe6e6;
    border: 2px solid #cc0000;
    border-radius: 10px;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .ban-box h2 {
    color: #b30000;
    margin-bottom: 15px;
    }
    .ban-box p {
    font-size: 14px;
    color: #333;
    }
    .btn-voltar {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background: #cc0000;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.3s;
    }
    .btn-voltar:hover {
    background: #990000;
    }
    </style>
</head>
<body>
    <div class="ban-box">
        <h2>⚠ Infelizmente sua conta está banida, <?php echo htmlspecialchars($nome); ?></h2>
            <?php if ($data_fim) { ?>
            <p>O banimento expira em <strong><?php echo $data_fim->format('Y-m-d H:i:s'); ?></strong></p>
            <?php } else { ?>
            <p><strong>Este banimento é permanente.</strong></p>
            <?php } ?>
        <a class="btn-voltar" href="../html/loginCadastrov.html">Voltar</a>
    </div>
</body>
</html>