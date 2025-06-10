<?php
session_start(); // Inicia ou retorna uma sessão

// --- Dados de conexão com o banco de dados (anteriormente em conexao.php) ---
$host = 'localhost'; // Altere para o seu host do banco de dados
$dbname = 'project_servico';
$user = 'root';      // Altere para o seu usuário do banco de dados
$password = '';      // Altere para a sua senha do banco de dados

try {
    // É AQUI que a variável $pdo é criada e recebe o objeto de conexão PDO.
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Em caso de erro na conexão, registre o erro (para depuração)
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());

    // E encerre o script, exibindo uma mensagem genérica para o usuário
    // Em produção, evite mostrar detalhes do erro do banco de dados.
    die("Desculpe, ocorreu um problema ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}
// --- Fim da seção de conexão ---


// Obtém o e-mail (ou nome de usuário) e a senha enviados via POST
$identificador = $_POST['email'] ?? ''; // Campo 'email' do formulário será usado para ambos
$senha_digitada = $_POST['senha'] ?? '';

// --- Tentativa de Login como CLIENTE ---
// Um cliente loga com email_cliente e senha_cliente
try {
    $sql_cliente = "SELECT IDcliente_PK, nome_cliente, email_cliente, senha_cliente FROM cliente WHERE email_cliente = :identificador";
    $stmt_cliente = $pdo->prepare($sql_cliente);
    $stmt_cliente->bindParam(':identificador', $identificador);
    $stmt_cliente->execute();
    $result_cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

    if ($result_cliente) { // Se encontrou um cliente com o e-mail
        // Verifica a senha hasheada
        if (password_verify($senha_digitada, $result_cliente['senha_cliente'])) {
            $_SESSION['id'] = $result_cliente['IDcliente_PK'];
            $_SESSION['nome'] = $result_cliente['nome_cliente'];
            $_SESSION['tipo'] = 'cliente'; // Define o tipo de usuário como 'cliente'

            header("Location: ../cliente/painel_cliente.php");
            exit(); // É crucial usar exit() após header() para garantir o redirecionamento
        }
    }

    // --- Se não logou como CLIENTE, tenta como FUNCIONÁRIO ---
    // Um funcionário loga com nome_funcionario e senha_funcionario.
    // Usamos o campo 'email' do formulário para o 'nome_funcionario' aqui.
    $sql_funcionario = "SELECT IDfuncionario_PK, nome_funcionario, senha_funcionario, categoria FROM funcionario WHERE nome_funcionario = :identificador";
    $stmt_funcionario = $pdo->prepare($sql_funcionario);
    $stmt_funcionario->bindParam(':identificador', $identificador);
    $stmt_funcionario->execute();
    $result_funcionario = $stmt_funcionario->fetch(PDO::FETCH_ASSOC);

    if ($result_funcionario) { // Se encontrou um funcionário com o nome de usuário
        // Verifica a senha hasheada
        if (password_verify($senha_digitada, $result_funcionario['senha_funcionario'])) {
            $_SESSION['id'] = $result_funcionario['IDfuncionario_PK'];
            $_SESSION['nome'] = $result_funcionario['nome_funcionario'];
            $_SESSION['tipo'] = 'funcionario'; // Define o tipo de usuário como 'funcionario'

            header("Location: ../funcionario/painel_funcionario.php");
            exit();
        }
    }

    // --- Se nenhuma das tentativas de login teve sucesso ---
    echo "E-mail/Nome de usuário ou senha inválidos.";
    echo "<a href = '../html/loginCadastro.html'> Voltar </a>";

} catch (PDOException $e) {
    // Loga o erro para depuração (não exiba o erro diretamente para o usuário em produção)
    error_log("Erro no processo de login: " . $e->getMessage());
    echo "Ocorreu um erro ao tentar fazer login. Por favor, tente novamente mais tarde.";
    echo "<a href = '../html/loginCadastro.html'> Voltar </a>";
}
?>