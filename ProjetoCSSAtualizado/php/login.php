<?php 
session_start();
include("conexao.php");

$nome = $_POST['nome'];
$senha = $_POST['senha']; 

$erro = null;   // mensagem de erro ou banimento
$data_fim = null;

// verifica se o usuario existe
$sql = "SELECT * FROM usuarios WHERE nome=? AND senha=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nome, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    // verifica na tabela de banimentos
    $id_usuario = $usuario['id'];
    $sql_ban = "SELECT * FROM banimentos 
                WHERE id_usuario = ? 
                ORDER BY id_banimento DESC 
                LIMIT 1";
    $stmt_ban = $conn->prepare($sql_ban);
    $stmt_ban->bind_param("i", $id_usuario);
    $stmt_ban->execute();
    $result_ban = $stmt_ban->get_result();

    if ($result_ban->num_rows > 0) {
        $ban = $result_ban->fetch_assoc();

        $agora = new DateTime();
        $data_fim = !empty($ban['data_fim']) ? new DateTime($ban['data_fim']) : null;

        // se ainda estiver banido
        if ($data_fim === null || $data_fim > $agora) {
            $erro = "banido";
        }
    }

    // se não estiver banido, faz login normalmente
    if (!$erro) {
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo'];

        if ($usuario['tipo'] === 'cliente') {
            header("Location: ../cliente/painel_cliente.php");
        } else {
            header("Location: ../funcionario/painel_funcionario.php");
        }
        exit;
    }

} else {
    $erro = "invalido"; // nome ou senha incorretos
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }
        .box {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .erro {
            background: #ffe6e6;
            border: 2px solid #cc0000;
        }
        .erro h2 {
            color: #b30000;
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

<div class="box erro">
    <?php if ($erro === "banido") { ?>
        <h2>⚠ Infelizmente sua conta está banida, <?= htmlspecialchars($nome) ?> 😔</h2>
        <?php if ($data_fim) { ?>
            <p>O banimento expira em <strong><?= $data_fim->format('d/m/Y H:i:s') ?></strong></p>
        <?php } else { ?>
            <p><strong>Este banimento é permanente.</strong></p>
        <?php } ?>
    <?php } else { ?>
        <h2> Nome ou senha inválidos</h2>
        <p>Verifique seus dados e tente novamente.</p>
    <?php } ?>

    <a class="btn-voltar" href="../html/loginCadastro.html">Voltar</a>
</div>

</body>
</html>
