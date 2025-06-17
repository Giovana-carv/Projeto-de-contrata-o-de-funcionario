<?php
// Inclua seu arquivo de conexão com o banco de dados aqui, se tiver um separado.
// Exemplo: require_once 'db_connection.php';

// --- Configurações do Banco de Dados ---
// ATENÇÃO: Substitua 'seu_host', 'seu_usuario', 'sua_senha' e 'seu_banco_de_dados'
// pelas suas credenciais reais do MySQL.
define('DB_HOST', 'localhost'); // Geralmente 'localhost'
define('DB_USER', 'seu_usuario'); // Seu nome de usuário do MySQL
define('DB_PASS', 'sua_senha');   // Sua senha do MySQL
define('DB_NAME', 'seu_banco_de_dados'); // O nome do seu banco de dados

// Função para conectar ao banco de dados
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4"); // Garante a codificação UTF-8
    return $conn;
}

// Verifica se a requisição é um POST (envio do formulário)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtém o tipo de cadastro do campo oculto 'tipo'
    $tipo_cadastro = $_POST['tipo'] ?? '';

    // --- Lógica para Cadastro Pessoal ---
    if ($tipo_cadastro === 'pessoal') {
        // Conecta ao banco de dados
        $conn = connectDB();

        // Obtém e sanitiza os dados do formulário para o tipo 'pessoal'
        $nome_pessoa = $conn->real_escape_string($_POST['nome'] ?? '');
        $cpf_pessoa = $conn->real_escape_string($_POST['cpf_pessoal'] ?? '');
        $senha_pessoa_raw = $_POST['senha_pessoal'] ?? '';

        // Validação básica dos dados
        if (empty($nome_pessoa) || empty($cpf_pessoa) || empty($senha_pessoa_raw)) {
            echo "Erro: Todos os campos obrigatórios (Nome, CPF, Senha Pessoal) devem ser preenchidos.";
            $conn->close();
            exit();
        }

        // --- Segurança da Senha: Hashing ---
        // É CRÍTICO que você NUNCA armazene senhas em texto puro no banco de dados.
        // Use password_hash() para hash a senha de forma segura.
        $senha_hash = password_hash($senha_pessoa_raw, PASSWORD_DEFAULT);

        // Prepara a consulta SQL para inserir na tabela 'pessoa'
        // Usamos prepared statements para prevenir injeção de SQL
        $sql = "INSERT INTO pessoa (nome, cpf, senha_pessoa) VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo "Erro ao preparar a consulta: " . $conn->error;
            $conn->close();
            exit();
        }

        // 's' indica que os parâmetros são strings (nome, cpf, senha_hash)
        $stmt->bind_param("sss", $nome_pessoa, $cpf_pessoa, $senha_hash);

        // Executa a consulta
        if ($stmt->execute()) {
            echo "Cadastro pessoal realizado com sucesso!";
            // Opcional: Redirecionar o usuário para uma página de sucesso ou login
            // header("Location: sucesso.html");
            // exit();
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }

        // Fecha a conexão com o banco de dados e o statement
        $stmt->close();
        $conn->close();

    } elseif ($tipo_cadastro === 'cliente' || $tipo_cadastro === 'funcionario') {
        // --- Lógica para Cliente ou Funcionário (se você tiver) ---
        // Você precisaria de um bloco semelhante aqui para lidar com os campos específicos
        // de cliente e funcionário e inseri-los nas suas respectivas tabelas.
        // Por exemplo:
        // if ($tipo_cadastro === 'cliente') {
        //    // Inserir na tabela de clientes
        // } elseif ($tipo_cadastro === 'funcionario') {
        //    // Inserir na tabela de funcionários
        // }
        echo "Lógica para Cliente/Funcionário ainda não implementada neste script. Tipo selecionado: " . htmlspecialchars($tipo_cadastro);

    } else {
        echo "Tipo de cadastro inválido ou não especificado.";
    }

} else {
    // Se a requisição não for POST, significa que alguém tentou acessar o script diretamente.
    echo "Acesso inválido. Por favor, utilize o formulário de cadastro.";
}
?>