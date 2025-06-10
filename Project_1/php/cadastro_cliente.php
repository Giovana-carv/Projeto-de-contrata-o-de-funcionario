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
    $nome_cliente = $_POST['nome_cad'] ?? '';
    $email_cliente = $_POST['email'] ?? '';
    $senha_cliente = $_POST['senha_cad'] ?? '';
    // $cpf_cliente foi removido do formulário HTML, então não o esperamos mais aqui.
    // Se o IDpessoa for necessário para a tabela cliente, ele precisaria ser obtido de outra forma,
    // ou a coluna IDpessoa na tabela cliente deveria ser NULL.

    $nome_endereco_cliente = $_POST['nome_endereco_cliente'] ?? '';
    $nome_endereco_cnpj = $_POST['nome_endereco_cnpj'] ?? '';

    // Validação básica (sem o CPF)
    if (empty($nome_cliente) || empty($email_cliente) || empty($senha_cliente)) {
        echo json_encode(['success' => false, 'message' => 'Nome, E-mail e Senha são obrigatórios para o cadastro.']);
        exit();
    }

    // Hash da senha para segurança
    $senha_hashed = password_hash($senha_cliente, PASSWORD_DEFAULT);

    try {
        // Inserir dados na tabela cliente
        // Removido o campo 'CPF_pessoa' da inserção. Assumindo que IDpessoa é NULLABLE ou preenchido de outra forma.
        // Se IDpessoa na tabela cliente não puder ser NULL, esta operação falhará.
        $sql_cliente = "INSERT INTO cliente (nome_cliente, email_cliente, senha_cliente)
                         VALUES (:nome, :email, :senha)";
        $stmt_cliente = $pdo->prepare($sql_cliente);
        $stmt_cliente->bindParam(':nome', $nome_cliente);
        $stmt_cliente->bindParam(':email', $email_cliente);
        $stmt_cliente->bindParam(':senha', $senha_hashed); // Salva a senha hashed

        if ($stmt_cliente->execute()) {
            $cliente_id = $pdo->lastInsertId(); // Pega o ID do cliente recém-inserido

            // Inserir dados na tabela endereco
            $sql_endereco = "INSERT INTO endereco (IDcliente_FK, nome_endereco_cliente, nome_endereco_cnpj)
                             VALUES (:cliente_id, :endereco_cliente, :endereco_cnpj)";
            $stmt_endereco = $pdo->prepare($sql_endereco);
            $stmt_endereco->bindParam(':cliente_id', $cliente_id);
            $stmt_endereco->bindParam(':endereco_cliente', $nome_endereco_cliente);
            $stmt_endereco->bindParam(':endereco_cnpj', $nome_endereco_cnpj);

            if ($stmt_endereco->execute()) {
                // Não há $_SESSION['cpf_verificado'] para limpar, pois a verificação de CPF foi removida.
                echo json_encode(['success' => true, 'message' => 'Cadastro cadastral realizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar os endereços.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar os dados do cliente.']);
        }
    } catch (PDOException $e) {
        error_log("Erro no cadastro cadastral: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erro interno ao cadastrar.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
?>