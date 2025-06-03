<?php
session_start();

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
    $nome = $_POST['nome'];
    $senha_pessoa = $_POST['senha_pessoa'];
    $cpf = $_POST['CPF'];

    $sql_pessoa = "INSERT INTO pessoa (senha_pessoa, CPF, nome) VALUES (:senha, :cpf, :nome)";
    $stmt_pessoa = $pdo->prepare($sql_pessoa);
    $stmt_pessoa->bindParam(':nome', $nome);
    $stmt_pessoa->bindParam(':senha', $senha_pessoa);
    $stmt_pessoa->bindParam(':cpf', $cpf);

    if ($stmt_pessoa->execute()) {
        $pessoa_id = $pdo->lastInsertId();
        $_SESSION['cadastro_pessoal_sucesso'] = true;
        $_SESSION['pessoa_id'] = $pessoa_id;
    } else {
        $_SESSION['cadastro_pessoal_erro'] = true;
    }

    // Não redirecionamos, apenas voltamos para a página do formulário
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>