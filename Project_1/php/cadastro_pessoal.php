<?php
// Inclua seu arquivo de conexão com o banco de dados
include 'conexao.php'; // Certifique-se de que este arquivo existe

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $senha_pessoa = password_hash($_POST['senha_pessoa'], PASSWORD_DEFAULT); // Sempre hash a senha!
    $cpf = $_POST['CPF']; // O CPF virá do campo readonly

    // Validação básica
    if (empty($nome) || empty($senha_pessoa) || empty($cpf)) {
        // Tratar erro: dados incompletos
        echo "Erro: Por favor, preencha todos os campos obrigatórios.";
        exit;
    }

    // Remover caracteres não numéricos do CPF
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    try {
        // Verificar se o CPF já existe ANTES de inserir (redundância, mas boa prática)
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM pessoa WHERE CPF = ?");
        $stmt_check->execute([$cpf]);
        if ($stmt_check->fetchColumn() > 0) {
            echo "Erro: CPF já cadastrado. Por favor, utilize o Cadastro Cadastral.";
            exit;
        }

        // Inserir na tabela pessoa
        $stmt = $pdo->prepare("INSERT INTO pessoa (nome, senha_pessoa, CPF) VALUES (?, ?, ?)");
        if ($stmt->execute([$nome, $senha_pessoa, $cpf])) {
            echo "Cadastro pessoal realizado com sucesso!";
            // Redirecionar ou mostrar mensagem de sucesso
            // header('Location: ../loginCadastro.html?cadastro=sucesso'); // Exemplo
        } else {
            echo "Erro ao cadastrar pessoa.";
        }
    } catch (PDOException $e) {
        // Erro no banco de dados
        echo "Erro: " . $e->getMessage();
    }
} else {
    echo "Método de requisição inválido.";
}
?>