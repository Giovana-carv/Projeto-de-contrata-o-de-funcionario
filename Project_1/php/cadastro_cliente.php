<?php
session_start();

// ... (código de conexão com o banco de dados) ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_cliente = $_POST['nome_cad'];
    $email_cliente = $_POST['email'];
    $senha_cliente = $_POST['senha_cad'];
    $nome_endereco_cliente = isset($_POST['nome_endereco_cliente']) ? $_POST['nome_endereco_cliente'] : '';
    $nome_endereco_cnpj = isset($_POST['nome_endereco_cnpj']) ? $_POST['nome_endereco_cnpj'] : '';

    // *** VERIFICAÇÃO SE O CPF FOI VERIFICADO (OPCIONAL) ***
        if (!isset($_SESSION['cpf_verificado']) && !isset($_SESSION['pessoa_cpf'])) {
        $_SESSION['cadastro_cadastral_erro'] = "Erro: CPF não verificado.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
       exit();
    }
    $cpf_para_cadastro = $_SESSION['pessoa_cpf'] ?? ($_SESSION['cpf_verificado'] ?? null);
    if (!$cpf_para_cadastro) {
        $_SESSION['cadastro_cadastral_erro'] = "Erro: Informação de CPF não encontrada.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $sql_cliente = "INSERT INTO cliente (nome_cliente, email_cliente, senha_cliente, CPF_pessoa)
                    VALUES (:nome, :email, :senha, :cpf)";
    $stmt_cliente = $pdo->prepare($sql_cliente);
    $stmt_cliente->bindParam(':nome', $nome_cliente);
    $stmt_cliente->bindParam(':email', $email_cliente);
    $stmt_cliente->bindParam(':senha', $senha_cliente);
    $stmt_cliente->bindParam(':cpf', $cpf_para_cadastro);

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
            unset($_SESSION['cpf_verificado']); // Limpar a sessão após o cadastro
        } else {
            $_SESSION['cadastro_cadastral_erro'] = "Erro ao cadastrar os endereços.";
        }
    } else {
        $_SESSION['cadastro_cadastral_erro'] = "Erro ao cadastrar os dados do cliente.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER'] . "#signup");
    exit();
}
?>