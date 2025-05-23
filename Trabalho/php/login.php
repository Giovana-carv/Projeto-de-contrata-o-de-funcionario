<?php
session_start(); //inicia ou retorna uma sessão, que pode ser definida por um id via GET ou POST
include("conexao.php");

$nome = $_POST['nome'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuarios WHERE nome=? AND senha=?"; //seleciona na tabela 'usuarios' quando 'nome' e 'senha' são usados dados salvos no banco durante cadastro
$stmt = $conn->prepare($sql); //prepare($sql) -> prepara para executar instruções SQL com marcadores de posição
$stmt->bind_param("ss", $nome, $senha); //bind_param("ss", $nome, $senha) -> vincula as variaveis php aos marcadores de posição
$stmt->execute(); //execute -> executa a intrução; stmt -> variavel que armazena o objeto de instrução preparada
$result = $stmt->get_result(); //obtem os resultados

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc(); //fetch_assoc -> busca uma linha de dados do conjunto de resultados e a retorna como array associativo(nomes das colunas da tabela. Ex. chaves e valores das colunas)
    $_SESSION['id'] = $usuario['id']; //_SESSION -> armazena dados do usuario durante a sessão. Cada elemento é uma variavel que pode ser criada e acessada
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['tipo'] = $usuario['tipo'];

    if ($usuario['tipo'] === 'cliente') {
        header("Location: ../cliente/painel_cliente.php");
    } else {
        header("Location: ../funcionario/painel_funcionario.php");
    }
} else {
    echo "Nome ou senha inválidos.";
    echo "<a href = '../html/loginCadastro.html'> Voltar </a>";
}
?>