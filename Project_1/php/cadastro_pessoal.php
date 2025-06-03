<?php
session_start();

// Dados de conexão com o banco de dados
$host = 'seu_host'; // Altere para o seu host do banco de dados
$dbname = 'project_servico';
$user = 'seu_usuario'; // Altere para o seu usuário do banco de dados
$password = 'sua_senha'; // Altere para a sua senha do banco de dados

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'] ?? '';
    $senha_pessoa = $_POST['senha_pessoa'] ?? '';
    $cpf = $_POST['CPF'] ?? ''; // O CPF virá do campo readonly

    // Validação básica
    if (empty($nome) || empty($senha_pessoa) || empty($cpf) || strlen($cpf) !== 11) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
        exit();
    }

    // Hash da senha para segurança
    $senha_hashed = password_hash($senha_pessoa, PASSWORD_DEFAULT);

    try {
        $sql_pessoa = "INSERT INTO pessoa (senha_pessoa, CPF, nome) VALUES (:senha, :cpf, :nome)";
        $stmt_pessoa = $pdo->prepare($sql_pessoa);
        $stmt_pessoa->bindParam(':nome', $nome);
        $stmt_pessoa->bindParam(':senha', $senha_hashed); // Salva a senha hashed
        $stmt_pessoa->bindParam(':cpf', $cpf);

        if ($stmt_pessoa->execute()) {
            // O CPF já foi armazenado em $_SESSION['pessoa_cpf'] no verificar_cpf.php
            // Se precisar do ID da pessoa para algo futuro:
            // $pessoa_id = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Cadastro pessoal realizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar pessoa.']);
        }
    } catch (PDOException $e) {
        error_log("Erro no cadastro pessoal: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erro interno ao cadastrar.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
?>