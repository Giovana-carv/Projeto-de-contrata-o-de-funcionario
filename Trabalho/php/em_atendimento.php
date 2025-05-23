<?php
include('conexao.php');
session_start();

$id_funcionario = $_SESSION['id'];

// Ao concluir o serviço com upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_contratacao']) && isset($_FILES['imagem'])) {
    $id_contratacao = $_POST['id_contratacao'];

    // Upload da imagem
    $diretorio = "../uploads/";
    $nome_arquivo = uniqid() . "_" . $_FILES['imagem']['name'];
    $caminho = $diretorio . $nome_arquivo;
    move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho);

    // Atualiza status
    $stmt = $conn->prepare("UPDATE contratacoes SET status = 'finalizado' WHERE id = ? AND funcionario_id = ?");
    $stmt->bind_param("ii", $id_contratacao, $id_funcionario);
    $stmt->execute();

    // Registra foto na tabela fotos_servicos
    $stmt = $conn->prepare("INSERT INTO fotos_servicos (id_contratacao, caminho) VALUES (?, ?)");
    $stmt->bind_param("is", $id_contratacao, $caminho);
    $stmt->execute();

}

// Buscar serviços aceitos
$sql = "SELECT c.id, c.data_solicitacao, u.nome, u.endereco, u.foto_perfil
        FROM contratacoes c
        JOIN usuarios u ON c.cliente_id = u.id
        WHERE c.funcionario_id = ? AND c.status = 'aceito'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Serviços em Atendimento</h2>
<?php while ($row = $result->fetch_assoc()) { ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <img src="<?php echo $row['foto_perfil']; ?>" width="60" height="60" style="border-radius:50px;"><br>
        <strong>Cliente:</strong> <?php echo $row['nome']; ?><br>
        <strong>Endereço:</strong> <?php echo $row['endereco']; ?><br>
        <strong>Data:</strong> <?php echo $row['data_solicitacao']; ?><br>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_contratacao" value="<?php echo $row['id']; ?>">
            <label>Imagem do Serviço:</label><br>
            <input type="file" name="imagem" required><br><br>
            <button type="submit">Concluir Serviço</button>
        </form>
    </div>
<?php } 
echo "<a href='../funcionario/painel_funcionario.php'> Voltar</a>";
?>