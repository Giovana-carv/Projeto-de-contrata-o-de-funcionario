<?php
session_start();
if (!isset($_SESSION['pessoa_id'])) {
    die("Erro: ID da pessoa não encontrado.");
}
$pessoa_id_fk = $_SESSION['pessoa_id'];

// Dados de conexão com o banco de dados
$host = 'localhost'; // Endereço do servidor MySQL
$dbname = 'project_servico'; // Nome do banco de dados do SQL
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
    $senha_cliente = $_POST['senha_cad']; // Também é recomendável hashear esta senha
    $nome_endereco_cliente = isset($_POST['nome_endereco_cliente']) ? $_POST['nome_endereco_cliente'] : '';
    $nome_endereco_cnpj = isset($_POST['nome_endereco_cnpj']) ? $_POST['nome_endereco_cnpj'] : '';

    // Inserir dados na tabela cliente
    $sql_cliente = "INSERT INTO cliente (nome_cliente, email_cliente, senha_cliente, CPF_pessoa) 
                    VALUES (:nome, :email, :senha, (SELECT CPF FROM pessoa WHERE IDpessoa_PK = :pessoa_id))";
    $stmt_cliente = $pdo->prepare($sql_cliente);
    $stmt_cliente->bindParam(':pessoa_id', $pessoa_id_fk);
    $stmt_cliente->bindParam(':nome', $nome_cliente);
    $stmt_cliente->bindParam(':email', $email_cliente);
    $stmt_cliente->bindParam(':senha', $senha_cliente); // Em produção, use password_hash()

    if ($stmt_cliente->execute()) {
        $cliente_id = $pdo->lastInsertId(); // Obtém o IDcliente_PK inserido
        echo "Dados do cliente cadastrados com sucesso. ID: " . $cliente_id . "<br>";

        // Inserir dados na tabela endereco
        $sql_endereco = "INSERT INTO endereco (IDcliente_FK, nome_endereco_cliente, nome_endereco_cnpj) 
                         VALUES (:cliente_id, :endereco_cliente, :endereco_cnpj)";
        $stmt_endereco = $pdo->prepare($sql_endereco);
        $stmt_endereco->bindParam(':cliente_id', $cliente_id);
        $stmt_endereco->bindParam(':endereco_cliente', $nome_endereco_cliente);
        $stmt_endereco->bindParam(':endereco_cnpj', $nome_endereco_cnpj);

        if ($stmt_endereco->execute()) {
            echo "Endereços cadastrados com sucesso.";
        } else {
            echo "Erro ao cadastrar os endereços.";
        }

    } else {
        echo "Erro ao cadastrar os dados do cliente.";
    }
}
?>