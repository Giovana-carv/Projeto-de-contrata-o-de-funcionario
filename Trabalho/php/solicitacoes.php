<?php
include('conexao.php');
session_start();

$id_funcionario = $_SESSION['id'];

// Ação de aceitar ou recusar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'], $_POST['id_contratacao'])) {
    $acao = $_POST['acao'] === 'aceitar' ? 'aceito' : 'recusado';
    $id_contratacao = (int) $_POST['id_contratacao'];

    $sql_update = "UPDATE contratacoes SET status = ? WHERE id = ? AND funcionario_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sii", $acao, $id_contratacao, $id_funcionario);
    $stmt_update->execute();
}

// Busca solicitações pendentes
$sql = "SELECT c.id AS id_contratacao, u.nome, u.endereco, u.foto_perfil
        FROM contratacoes c
        JOIN usuarios u ON c.cliente_id = u.id
        WHERE c.funcionario_id = ? 
        AND c.status = 'pendente'
        AND u.tipo = 'cliente'";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Solicitações de Contratação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .solicitacao {
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 1px 1px 6px rgba(0,0,0,0.1);
        }
        .foto {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
        .botoes button {
            padding: 8px 16px;
            margin-right: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .aceitar {
            background-color: #4CAF50;
            color: white;
        }
        .recusar {
            background-color: #f44336;
            color: white;
        }
        a {
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Solicitações de Contratação</h2>

<?php if ($result->num_rows > 0) { ?>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="solicitacao">
            <img src="<?php echo htmlspecialchars($row['foto_perfil']); ?>" class="foto" alt="Foto de perfil"><br><br>
            <strong>Nome:</strong> <?php echo htmlspecialchars($row['nome']); ?><br>
            <strong>Endereço:</strong> <?php echo htmlspecialchars($row['endereco']); ?><br><br>

            <form method="POST" class="botoes">
                <input type="hidden" name="id_contratacao" value="<?php echo $row['id_contratacao']; ?>">
                <button type="submit" name="acao" value="aceitar" class="aceitar">Aceitar</button>
                <button type="submit" name="acao" value="recusar" class="recusar">Recusar</button>
            </form>
        </div>
    <?php } ?>
<?php } else { ?>
    <p>Nenhuma solicitação pendente encontrada.</p>
<?php } ?>

<a href="../funcionario/painel_funcionario.php">← Voltar</a>
</body>
</html>