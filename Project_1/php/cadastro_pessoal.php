<?php
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
    $nome = $_POST['nome'];
    $senha_pessoa = $_POST['senha_pessoa']; // É recomendável hashear a senha antes de salvar
    $cpf = $_POST['CPF'];
    $cnpj = $_POST['cnpj'];

    // Inserir dados na tabela pessoa
    $sql_pessoa = "INSERT INTO pessoa (senha_pessoa, CPF, nome) VALUES (:senha, :cpf, :nome)";
    $stmt_pessoa = $pdo->prepare($sql_pessoa);
    $stmt_pessoa->bindParam(':nome', $nome);
    $stmt_pessoa->bindParam(':senha', $senha_pessoa); // Em produção, use password_hash()
    $stmt_pessoa->bindParam(':cpf', $cpf);
    // Não há coluna 'cnpj' na tabela pessoa, então não vamos inserir esse valor aqui.

    if ($stmt_pessoa->execute()) {
        $pessoa_id = $pdo->lastInsertId(); // Obtém o IDpessoa_PK inserido
        echo "Dados da pessoa cadastrados com sucesso. ID: " . $pessoa_id . "<br>";
        // Você pode redirecionar o usuário para o próximo formulário (Cadastro Cadastral) aqui
        session_start();
        $_SESSION['pessoa_id'] = $pessoa_id;
        // header("Location: proxima_pagina_cadastro_cadastral.php");
        exit();
    } else {
        echo "Erro ao cadastrar os dados da pessoa.";
    }
}
?>