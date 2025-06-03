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
    $nome_cliente = $_POST['nome_cad'] ?? '';
    $email_cliente = $_POST['email'] ?? '';
    $senha_cliente = $_POST['senha_cad'] ?? '';
    $cpf_cliente = $_POST['cpf_cliente'] ?? ''; // O CPF virá do campo readonly
    $nome_endereco_cliente = isset($_POST['nome_endereco_cliente']) ? $_POST['nome_endereco_cliente'] : '';
    $nome_endereco_cnpj = isset($_POST['nome_endereco_cnpj']) ? $_POST['nome_endereco_cnpj'] : '';

    // Validação básica
    if (empty($nome_cliente) || empty($email_cliente) || empty($senha_cliente) || empty($cpf_cliente) || strlen($cpf_cliente) !== 11) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos ou CPF não informado.']);
        exit();
    }

    // Verifica se o CPF foi realmente verificado anteriormente na sessão
    // Isso é uma camada extra de segurança, já que o CPF veio do input readonly
    if (!isset($_SESSION['cpf_verificado']) || $_SESSION['cpf_verificado'] !== $cpf_cliente) {
        // Se o CPF não foi verificado ou não corresponde, pode ser uma tentativa de manipulação
        echo json_encode(['success' => false, 'message' => 'Erro: CPF não verificado ou inválido.']);
        exit();
    }

    // Hash da senha para segurança
    $senha_hashed = password_hash($senha_cliente, PASSWORD_DEFAULT);

    try {
        // Primeiro, obtemos o ID da pessoa com base no CPF (se já existir)
        $stmt_get_pessoa_id = $pdo->prepare("SELECT IDpessoa FROM pessoa WHERE CPF = :cpf");
        $stmt_get_pessoa_id->bindParam(':cpf', $cpf_cliente);
        $stmt_get_pessoa_id->execute();
        $pessoa_id = $stmt_get_pessoa_id->fetchColumn();

        if (!$pessoa_id) {
            // Se a pessoa não existir (o que não deveria acontecer se a lógica de verificação estiver correta),
            // podemos inserir a pessoa aqui ou retornar um erro.
            // Para simplificar, assumimos que a pessoa já foi criada via "Cadastro Pessoal" ou existe.
            echo json_encode(['success' => false, 'message' => 'Pessoa com este CPF não encontrada no sistema.']);
            exit();
        }

        $sql_cliente = "INSERT INTO cliente (nome_cliente, email_cliente, senha_cliente, CPF_pessoa)
                        VALUES (:nome, :email, :senha, :cpf)";
        $stmt_cliente = $pdo->prepare($sql_cliente);
        $stmt_cliente->bindParam(':nome', $nome_cliente);
        $stmt_cliente->bindParam(':email', $email_cliente);
        $stmt_cliente->bindParam(':senha', $senha_hashed); // Salva a senha hashed
        $stmt_cliente->bindParam(':cpf', $cpf_cliente); // Usar o CPF diretamente

        if ($stmt_cliente->execute()) {
            $cliente_id = $pdo->lastInsertId();
            $sql_endereco = "INSERT INTO endereco (IDcliente_FK, nome_endereco_cliente, nome_endereco_cnpj)
                             VALUES (:cliente_id, :endereco_cliente, :endereco_cnpj)";
            $stmt_endereco = $pdo->prepare($sql_endereco);
            $stmt_endereco->bindParam(':cliente_id', $cliente_id);
            $stmt_endereco->bindParam(':endereco_cliente', $nome_endereco_cliente);
            $stmt_endereco->bindParam(':endereco_cnpj', $nome_endereco_cnpj);

            if ($stmt_endereco->execute()) {
                // Limpar a sessão após o cadastro bem-sucedido
                unset($_SESSION['cpf_verificado']);
                unset($_SESSION['pessoa_cpf']);
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