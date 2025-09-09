<?php
session_start();
include("../php/conexao.php");

// Redireciona se não for cliente logado
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_cliente = $_SESSION['id'];

// Cliente clicou em "apagar" a notificação
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_contratacao'])) {
    $id_contratacao = (int)$_POST['id_contratacao'];

    // Verifica se a notificação pertence ao cliente atual
    $verifica = $conn->prepare("SELECT id FROM contratacoes WHERE id = ? AND id_cliente = ?");
    $verifica->bind_param("ii", $id_contratacao, $id_cliente);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        // "Apaga" a notificação (define como string vazia)
        $apagar = $conn->prepare("UPDATE contratacoes SET notificacao_cliente = '' WHERE id = ?");
        $apagar->bind_param("i", $id_contratacao);
        $apagar->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Consulta notificações ainda visíveis
$sql = "SELECT id, notificacao_cliente, data_solicitacao 
        FROM contratacoes 
        WHERE id_cliente = ? AND notificacao_cliente != ''
        ORDER BY data_solicitacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>🔔 Respostas dos Pedidos</h2>

<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px; background-color: #fff0f0;">
            <p><?= htmlspecialchars($row['notificacao_cliente']) ?></p>
            <small><?= date('d/m/Y H:i', strtotime($row['data_solicitacao'])) ?></small>

            <!-- Botão de apagar -->
            <form method="post" style="display:inline;">
                <input type="hidden" name="id_contratacao" value="<?= $row['id'] ?>">
                <button type="submit" style="margin-left: 10px; background-color: #ff416c; color:white; border:none; padding:5px 10px; border-radius:5px; cursor: pointer;">
                    Apagar
                </button>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Você não tem notificações.</p>
<?php endif; ?>

<!-- Botão para voltar -->
<form action='../cliente/painel_cliente.php' method='get'>
    <button type='submit' style="background-color: #ff416c; margin-left: 10px; color:white; border:none; padding:5px 10px; border-radius:5px; cursor: pointer;">
        Voltar</button>
</form>
