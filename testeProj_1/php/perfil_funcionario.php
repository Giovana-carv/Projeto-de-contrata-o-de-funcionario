<?php
include('conexao.php');
session_start();

$id_funcionario = $_SESSION['id'];

$sql_fotos = "SELECT f.caminho, c.data_solicitacao, u.nome AS cliente_nome
              FROM fotos_servicos f
              JOIN contratacoes c ON f.id_contratacao = c.id
              JOIN usuarios u ON c.cliente_id = u.id
              WHERE c.funcionario_id = ? AND c.status = 'finalizado'
              ORDER BY c.data_solicitacao DESC";

$stmt_fotos = $conn->prepare($sql_fotos);

if (!$stmt_fotos) {
    die("Erro na preparação da consulta de fotos: " . $conn->error);
}

$stmt_fotos->bind_param("i", $id_funcionario);
$stmt_fotos->execute();
$result_fotos = $stmt_fotos->get_result();
?>

<h2>Serviços Antigos com Imagens</h2>

<?php if ($result_fotos->num_rows > 0) { ?>
    <ul>
        <?php while ($row = $result_fotos->fetch_assoc()) { ?>
            <li>
                <strong>Cliente:</strong> <?php echo htmlspecialchars($row['cliente_nome']); ?><br>
                <strong>Data:</strong> <?php echo htmlspecialchars($row['data_solicitacao']); ?><br>
                <img src="<?php echo htmlspecialchars($row['caminho']); ?>" alt="Foto do serviço" style="width: 200px; height: auto;"><br>
                <hr>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p>Nenhuma imagem de serviço finalizado encontrada.</p>
<?php } ?>
<?php
echo "<a href='pesquisar.php'> Voltar</a>";?>