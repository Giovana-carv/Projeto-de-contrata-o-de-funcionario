<?php
session_start();

// Dados de conexão com o banco de dados
$host = 'localhost';
$dbname = 'project_servico';
$user = 'root';
$password = '';

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
    $nome_endereco_cliente = isset($_POST['nome_endereco_cliente']) ? $_POST['nome_endereco_cliente'] : '';
    $nome_endereco_cnpj = isset($_POST['nome_endereco_cnpj']) ? $_POST['nome_endereco_cnpj'] : '';

    // Recupera o CPF da sessão (definido após o cadastro pessoal)
    if (!isset($_SESSION['pessoa_cpf'])) {
        $_SESSION['cadastro_cadastral_erro'] = "Erro: CPF da pessoa não encontrado. Realize o cadastro pessoal primeiro.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    $cpf_pessoa = $_SESSION['pessoa_cpf'];

    // Verifica se a pessoa já foi cadastrada na tabela 'pessoa'
    $sql_verifica_pessoa = "SELECT IDpessoa_PK FROM pessoa WHERE CPF = :cpf";
    $stmt_verifica_pessoa = $pdo->prepare($sql_verifica_pessoa);
    $stmt_verifica_pessoa->bindParam(':cpf', $cpf_pessoa);
    $stmt_verifica_pessoa->execute();

    if ($stmt_verifica_pessoa->rowCount() > 0) {
        $row_pessoa = $stmt_verifica_pessoa->fetch(PDO::FETCH_ASSOC);
        $pessoa_id_fk = $row_pessoa['IDpessoa_PK'];

        $sql_cliente = "INSERT INTO cliente (nome_cliente, email_cliente, senha_cliente, CPF_pessoa)
                          VALUES (:nome, :email, :senha, :cpf_fk)";
        $stmt_cliente = $pdo->prepare($sql_cliente);
        $stmt_cliente->bindParam(':nome', $nome_cliente);
        $stmt_cliente->bindParam(':email', $email_cliente);
        $stmt_cliente->bindParam(':senha', $senha_cliente);
        $stmt_cliente->bindParam(':cpf_fk', $cpf_pessoa);

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
    } else {
        $_SESSION['cadastro_cadastral_erro'] = "Erro: Cadastro pessoal não encontrado para este CPF.";
    }

    // Não redirecionamos, apenas voltamos para a página do formulário
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>