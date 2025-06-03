<?php
header('Content-Type: application/json');

// Inclua seu arquivo de conexão com o banco de dados aqui
// Exemplo:
include 'conexao.php'; // Certifique-se de que este arquivo existe e faz a conexão com seu banco de dados

$response = ['exists' => false];

// Pega o JSON enviado pelo JavaScript
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['cpf'])) {
    $cpf = $data['cpf'];

    // Validar e limpar o CPF (remova caracteres não numéricos)
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Conexão com o banco de dados (ajuste conforme sua configuração)
    // Exemplo usando MySQLi:
    $host = 'localhost';
    $db   = 'project_servico'; // Nome do seu banco de dados
    $user = 'root'; // Seu usuário do banco de dados
    $pass = ''; // Sua senha do banco de dados
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Prepara a consulta SQL para verificar o CPF na tabela 'pessoa'
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pessoa WHERE CPF = ?");
        $stmt->execute([$cpf]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $response['exists'] = true;
        }

    } catch (\PDOException $e) {
        // Erro na conexão ou consulta ao banco de dados
        error_log("Erro no banco de dados: " . $e->getMessage());
        $response['error'] = 'Erro ao conectar ao banco de dados ou executar a consulta.';
    }
} else {
    $response['error'] = 'CPF não fornecido.';
}

echo json_encode($response);
?>