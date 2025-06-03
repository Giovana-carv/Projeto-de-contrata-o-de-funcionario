<?php
// Inclua seu arquivo de conexão com o banco de dados
include 'conexao.php'; // Certifique-se de que este arquivo existe

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_cad = $_POST['nome_cad'];
    $email = $_POST['email'];
    $senha_cad = password_hash($_POST['senha_cad'], PASSWORD_DEFAULT); // Hash da senha!
    $cpf_cliente = $_POST['cpf_cliente']; // O CPF virá do campo readonly
    $nome_endereco_cliente = $_POST['nome_endereco_cliente'];
    $nome_endereco_cnpj = $_POST['nome_endereco_cnpj'];

    // Validação básica
    if (empty($nome_cad) || empty($email) || empty($senha_cad) || empty($cpf_cliente)) {
        echo "Erro: Por favor, preencha todos os campos obrigatórios.";
        exit;
    }

    // Remover caracteres não numéricos do CPF
    $cpf_cliente = preg_replace('/[^0-9]/', '', $cpf_cliente);

    try {
        // 1. Verificar se o CPF existe na tabela 'pessoa' e obter o IDpessoa_PK
        $stmt_pessoa = $pdo->prepare("SELECT IDpessoa_PK FROM pessoa WHERE CPF = ?");
        $stmt_pessoa->execute([$cpf_cliente]);
        $pessoa = $stmt_pessoa->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            echo "Erro: CPF não encontrado na base de dados de pessoas. Por favor, cadastre-se primeiro como pessoa.";
            exit;
        }
        $idPessoaFK = $pessoa['IDpessoa_PK'];

        // 2. Inserir na tabela cliente
        // Cuidado: a estrutura da sua tabela cliente no SQL tinha 'CPF_pessoa INT',
        // mas deveria ser 'CPF_pessoa VARCHAR(11)' se você quer usar o CPF diretamente
        // como FK, ou 'IDpessoa_FK INT' se quiser usar o IDpessoa_PK.
        // Considerando que você tem 'FOREIGN KEY (CPF_pessoa) REFERENCES pessoa(CPF)',
        // vou assumir que 'CPF_pessoa' na tabela 'cliente' deve ser o CPF String.
        // SE você quiser usar o IDpessoa_PK como FK, mude sua tabela cliente e o código aqui.
        // A melhor prática é usar IDpessoa_PK como FK em 'cliente'.
        // Se sua tabela 'cliente' tem 'CPF_pessoa', e você quer usar o CPF string como FK,
        // então o campo CPF da tabela 'pessoa' PRECISA ser uma chave primária ou ter um índice único.

        // Vamos usar o IDpessoa_PK como FK, que é a melhor prática.
        // Se sua tabela cliente não tiver IDpessoa_FK, adicione:
        // ALTER TABLE cliente ADD COLUMN IDpessoa_FK INT;
        // ALTER TABLE cliente ADD CONSTRAINT fk_cliente_pessoa FOREIGN KEY (IDpessoa_FK) REFERENCES pessoa(IDpessoa_PK);
        // E remova CPF_pessoa se não for usá-lo como FK.

        // Inserir na tabela cliente (assumindo que você adicionou IDpessoa_FK em cliente)
        $stmt_cliente = $pdo->prepare("INSERT INTO cliente (nome_cliente, email_cliente, senha_cliente, IDpessoa_FK) VALUES (?, ?, ?, ?)");
        if ($stmt_cliente->execute([$nome_cad, $email, $senha_cad, $idPessoaFK])) {
            $last_cliente_id = $pdo->lastInsertId();

            // 3. Inserir na tabela endereco
            if (!empty($nome_endereco_cliente) || !empty($nome_endereco_cnpj)) {
                $stmt_endereco = $pdo->prepare("INSERT INTO endereco (IDcliente_FK, nome_endereco_cliente, nome_endereco_cnpj) VALUES (?, ?, ?)");
                $stmt_endereco->execute([$last_cliente_id, $nome_endereco_cliente, $nome_endereco_cnpj]);
            }
            echo "Cadastro cadastral realizado com sucesso!";
            // Redirecionar ou mostrar mensagem de sucesso
            // header('Location: ../loginCadastro.html?cadastro=sucesso'); // Exemplo
        } else {
            echo "Erro ao cadastrar cliente.";
        }

    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
} else {
    echo "Método de requisição inválido.";
}
?>