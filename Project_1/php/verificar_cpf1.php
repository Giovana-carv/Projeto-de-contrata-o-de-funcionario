<?php
session_start();

// Dados de conexão com o banco de dados (substitua pelos seus)
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
    $cpf_verificar = $_POST['cpf_verificar'];

    $sql_check_cpf = "SELECT CPF FROM pessoa WHERE CPF = :cpf";
    $stmt_check_cpf = $pdo->prepare($sql_check_cpf);
    $stmt_check_cpf->bindParam(':cpf', $cpf_verificar);
    $stmt_check_cpf->execute();

    if ($stmt_check_cpf->rowCount() > 0) {
        // CPF encontrado, redirecionar para o formulário de cadastro cadastral
        $_SESSION['cpf_verificado'] = $cpf_verificar; // Opcional: guardar o CPF para usar no cadastro
        header("Location: loginCadastro.html#signup"); // Redireciona para a seção de cadastro
        exit();
    } else {
        // CPF não encontrado, exibir erro
        $_SESSION['cpf_nao_encontrado'] = "CPF não encontrado. Por favor, complete o Cadastro Pessoal primeiro.";
        header("Location: verificar_cpf_cadastro_cadastral.html");
        exit();
    }
} else {
    // Acesso direto ao arquivo não permitido
    header("Location: loginCadastro.html");
    exit();
}
?>