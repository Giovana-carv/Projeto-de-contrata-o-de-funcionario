<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$cliente_id = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $funcionario_id = (int) $_POST['funcionario_id'];

    // Verifica se o funcionário existe
    $verifica = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND tipo = 'funcionario'");
    $verifica->bind_param("i", $funcionario_id);
    $verifica->execute();
    $resultado = $verifica->get_result();

    if ($resultado->num_rows === 0) {
        echo "Funcionário não encontrado.";
        exit;
    }

    // Insere a contratação
    $stmt = $conn->prepare("INSERT INTO contratacoes (id_cliente, id_funcionario, notificacao_funcionario) VALUES (?, ?, TRUE)");
    $stmt->bind_param("ii", $cliente_id, $funcionario_id);
    $stmt->execute();

    //echo "Pedido enviado com sucesso!";
    //header("Location: ../cliente/painel.php");
    //exit;
   //teste
    echo "Pedido enviado com sucesso! Redirecionando...";
echo "<script>
    setTimeout(function(){
        window.location.href = '../cliente/painel_cliente.php';
    }, 2000);
</script>";
exit;

}
?>
