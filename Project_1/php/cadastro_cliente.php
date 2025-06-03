<?php
session_start();

if (!isset($_SESSION['pessoa_id'])) {
    $_SESSION['cadastro_cadastral_erro'] = "Erro: ID da pessoa não encontrado.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
$pessoa_id_fk = $_SESSION['pessoa_id'];

// Dados de conexão com o banco de dados
$host = 'seu_host';
$dbname = 'project_servico';
$user = 'seu_usuario';
$password = 'sua_senha';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_cliente = $_POST['nome_cad'];
    $email_cliente = $_POST['email'];
    $senha_cliente = $_POST['senha_cad'];
    $nome_endereco_cliente = isset($_POST['nome_endereco_pessoa']) ? $_POST['nome_endereco_pessoa'] : '';
    $nome_endereco_cnpj = isset($_POST['nome_endereco_cnpj']) ? $_POST['nome_endereco_cnpj'] : '';

    $sql_cliente = "INSERT INTO cliente (nome_cliente, email_cliente, senha_cliente, CPF_pessoa) 
                    VALUES (:nome, :email, :senha, (SELECT CPF FROM pessoa WHERE IDpessoa_PK = :pessoa_id))";
    $stmt_cliente = $pdo->prepare($sql_cliente);
    $stmt_cliente->bindParam(':pessoa_id', $pessoa_id_fk);
    $stmt_cliente->bindParam(':nome', $nome_cliente);
    $stmt_cliente->bindParam(':email', $email_cliente);
    $stmt_cliente->bindParam(':senha', $senha_cliente);

    if ($stmt_cliente->execute()) {
        $cliente_id = $pdo->lastInsertId();
        $sql_endereco = "INSERT INTO endereco (IDcliente_FK, nome_endereco_cliente, nome_endereco_cnpj) 
                         VALUES (:cliente_id, :endereco_cliente, :endereco_cnpj)";
        $stmt_endereco = $pdo->prepare($sql_endereco);
        $stmt_endereco->bindParam(':cliente_id', $cliente_id);
        $stmt_endereco->bindParam(':endereco_cliente', $nome_endereco_cliente);
        $stmt_endereco->bindParam(':endereco_cnpj', $nome_endereco_cnpj);

        if ($stmt_endereco->execute()) {
            $_SESSION['cadastro_cadastral_sucesso'] = true;
        } else {
            $_SESSION['cadastro_cadastral_erro'] = "Erro ao cadastrar os endereços.";
        }
    } else {
        $_SESSION['cadastro_cadastral_erro'] = "Erro ao cadastrar os dados do cliente.";
    }

    // Não redirecionamos, apenas voltamos para a página do formulário
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>