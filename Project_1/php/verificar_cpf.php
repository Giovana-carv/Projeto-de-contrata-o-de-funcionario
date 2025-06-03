<?php
session_start();

// Dados de conexão com o banco de dados
$host = 'seu_host'; // Altere para o seu host do banco de dados
$dbname = 'project_servico';
$user = 'seu_usuario'; // Altere para o seu usuário do banco de dados
$password = 'sua_senha'; // Altere para a sua senha do banco de dados

// Configuração da conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em um ambiente de produção, registre o erro em vez de exibi-lo diretamente
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    echo json_encode(['exists' => false, 'error' => 'Erro interno do servidor.']);
    exit();
}

// Verifica se a requisição é POST e se o CPF foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cpf'])) {
    $cpf = $_POST['cpf'];

    // Remove caracteres não numéricos do CPF
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Validação básica do CPF (pode ser aprimorada com um validador de CPF mais robusto)
    if (strlen($cpf) !== 11) {
        echo json_encode(['exists' => false, 'error' => 'CPF inválido.']);
        exit();
    }

    try {
        // Prepara a consulta para verificar se o CPF existe na tabela 'pessoa'
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pessoa WHERE CPF = :cpf");
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        // Se o CPF existir, retorna true, caso contrário, false
        if ($count > 0) {
            $_SESSION['cpf_verificado'] = $cpf; // Armazena o CPF na sessão
            echo json_encode(['exists' => true, 'cpf' => $cpf]);
        } else {
            $_SESSION['pessoa_cpf'] = $cpf; // Armazena o CPF na sessão para novo cadastro
            echo json_encode(['exists' => false, 'cpf' => $cpf]);
        }
    } catch (PDOException $e) {
        // Em caso de erro na consulta SQL
        error_log("Erro na consulta SQL: " . $e->getMessage());
        echo json_encode(['exists' => false, 'error' => 'Erro ao consultar o banco de dados.']);
    }
} else {
    // Se a requisição não for POST ou o CPF não foi enviado
    echo json_encode(['exists' => false, 'error' => 'Requisição inválida.']);
}
?>