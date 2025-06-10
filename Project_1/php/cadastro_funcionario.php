<?php
session_start(); // Inicia ou retorna uma sessão

// Inclui o arquivo de conexão com o banco de dados.
// Certifique-se de que 'conexao.php' está configurado para conectar ao 'project_servico'.
include("conexao.php"); 

// Obtém o e-mail e a senha enviados via POST
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// --- Tentativa de Login como CLIENTE ---
$sql_cliente = "SELECT IDcliente_PK, nome_cliente, email_cliente, senha_cliente FROM cliente WHERE email_cliente = ?";
$stmt_cliente = $pdo->prepare($sql_cliente); // Usando $pdo do seu conexao.php
$stmt_cliente->bindParam(1, $email);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC); // Obtém a linha como array associativo

if ($result_cliente) { // Se encontrou um cliente com o e-mail
    if (password_verify($senha, $result_cliente['senha_cliente'])) { // Verifica a senha hashed
        $_SESSION['id'] = $result_cliente['IDcliente_PK'];
        $_SESSION['nome'] = $result_cliente['nome_cliente'];
        $_SESSION['tipo'] = 'cliente'; // Define o tipo de usuário

        header("Location: ../cliente/painel_cliente.php");
        exit(); // É importante usar exit() após header()
    }
}

// --- Se não logou como CLIENTE, tenta como FUNCIONÁRIO ---
// Para funcionários, vamos usar o nome_funcionario e senha_funcionario.
// Se você quiser login por e-mail para funcionário, a coluna 'email_funcionario' DEVE existir na tabela 'funcionario'.
// Por enquanto, vamos usar o nome_funcionario como "email" para o campo de entrada no login,
// mas a comparação será feita com 'nome_funcionario' no banco de dados.

$sql_funcionario = "SELECT IDfuncionario_PK, nome_funcionario, senha_funcionario FROM funcionario WHERE nome_funcionario = ?";
$stmt_funcionario = $pdo->prepare($sql_funcionario);
$stmt_funcionario->bindParam(1, $email); // Usamos $email aqui, mas ele representa o 'nome_funcionario' para este caso
$stmt_funcionario->execute();
$result_funcionario = $stmt_funcionario->fetch(PDO::FETCH_ASSOC);

if ($result_funcionario) { // Se encontrou um funcionário com o nome
    if (password_verify($senha, $result_funcionario['senha_funcionario'])) { // Verifica a senha hashed
        $_SESSION['id'] = $result_funcionario['IDfuncionario_PK'];
        $_SESSION['nome'] = $result_funcionario['nome_funcionario'];
        $_SESSION['tipo'] = 'funcionario'; // Define o tipo de usuário

        header("Location: ../funcionario/painel_funcionario.php");
        exit();
    }
}

// --- Se nenhuma das tentativas de login teve sucesso ---
echo "E-mail/Nome ou senha inválidos.";
echo "<a href = '../html/loginCadastro.html'> Voltar </a>";

?>