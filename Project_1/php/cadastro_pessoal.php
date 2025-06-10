<?php
session_start();

// Dados de conexão com o banco de dados
$host = 'localhost'; // Altere para o seu host do banco de dados
$dbname = 'project_servico';
$user = 'root'; // Altere para o seu usuário do banco de dados
$password = ''; // Altere para a sua senha do banco de dados

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // As variáveis que vêm do formulário (do HTML)
    $nome = $_POST['nome'] ?? '';             // Mapeia para a coluna 'nome' da tabela 'pessoa'
    $senha_pessoa = $_POST['senha_pessoa'] ?? ''; // Mapeia para a coluna 'senha_pessoa' da tabela 'pessoa'
    $cpf = $_POST['CPF'] ?? '';               // Mapeia para a coluna 'CPF' da tabela 'pessoa'

    // Validação básica
    if (empty($nome) || empty($senha_pessoa) || empty($cpf) || strlen($cpf) !== 11) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos. Todos os campos são obrigatórios e o CPF deve ter 11 dígitos.']);
        exit();
    }

    // Hash da senha para segurança
    $senha_hashed = password_hash($senha_pessoa, PASSWORD_DEFAULT);

    try {
        // A query SQL está inserindo nas colunas corretas da tabela 'pessoa'
        $sql_pessoa = "INSERT INTO pessoa (senha_pessoa, CPF, nome) VALUES (:senha, :cpf, :nome)";
        $stmt_pessoa = $pdo->prepare($sql_pessoa);
        
        // Binda os parâmetros da query com as variáveis PHP
        $stmt_pessoa->bindParam(':nome', $nome);
        $stmt_pessoa->bindParam(':senha', $senha_hashed); // Salva a senha hashed
        $stmt_pessoa->bindParam(':cpf', $cpf);

        if ($stmt_pessoa->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cadastro pessoal realizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar pessoa.']);
        }
    } catch (PDOException $e) {
        // Erro de SQL (ex: CPF duplicado, campos nulos indevidos, etc.)
        error_log("Erro no cadastro pessoal: " . $e->getMessage());
        
        // Verifica se o erro é de CPF duplicado
        if ($e->getCode() == '23000') { // Código SQLSTATE para violação de integridade (duplicate entry)
            echo json_encode(['success' => false, 'message' => 'CPF já cadastrado. Por favor, utilize outro CPF ou faça login.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro interno ao cadastrar.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
?>