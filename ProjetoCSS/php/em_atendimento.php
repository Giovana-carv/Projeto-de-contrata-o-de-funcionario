<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'funcionario') {
    header("Location: ../html/loginCadastro.html");
    exit;
}

$id_funcionario = $_SESSION['id'];

$sql = "SELECT c.*, u.nome AS nome_cliente, u.foto_perfil
        FROM contratacoes c
        JOIN usuarios u ON c.id_cliente = u.id
        WHERE c.id_funcionario = ? AND c.status = 'em atendimento' AND c.acao = 'aceito'
        ORDER BY c.data_solicitacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_funcionario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/temaEScuro.css">
    <title>Serviços em Atendimento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin: 15px auto;
            max-width: 500px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: translateX(5px);
        }
        .cliente-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .cliente-info img {
            border-radius: 50%;
            border: 2px solid #ccc;
        }
        .cliente-nome {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            color: #555;
        }
        input[type="file"] {
            margin-top: 5px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
        }
        .btn-finalizar {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            margin-top: 15px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }
        .btn-finalizar:hover {
            background-color: #1f7f35;
        }
        .btn-voltar {
            display: block;
            text-align: center;
            margin: 30px auto 0;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            max-width: 150px;
            transition: background-color 0.2s ease;
        }
        .btn-voltar:hover {
            background-color: #0056b3;
        }
        /* ====== Botão Tema ====== */
.theme-toggle {
    position: fixed;
    top: 15px;
    right: 15px;
    border: none;
    background: var(--card-bg, #fff);
    border-radius: 50%;
    padding: 10px;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: background 0.3s ease, transform 0.2s ease;
}
.theme-toggle:hover {
    transform: rotate(20deg);
}

 /*Tema escuro*/
    .theme-toggle {
      background:rgba(255,255,255,0.2);
      border-radius:50%;
      width:40px;
      height:40px;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .theme-toggle:hover {
      background:rgba(255,255,255,0.35);
      cursor: pointer;
    }
    body.dark {
      background:#1e1e1e;
      color: black;
    }
    body.dark h2 {
      color:white;
    }
    
    form input[type="file"]{
        color: white;
    }
    </style>
</head>
<body>

<button class="theme-toggle" id="theme-toggle">🌙</button>

<h2>Serviços em Atendimento</h2>

<?php while($row = $result->fetch_assoc()): ?>
    <div class="card">
        <div class="cliente-info">
            <?php if (!empty($row['foto_perfil'])): ?>
                <img src="../uploads/<?= htmlspecialchars($row['foto_perfil']) ?>" 
                     alt="Foto do Cliente" width="80" height="80">
            <?php else: ?>
                <img src="../uploads/default.png" alt="Sem foto" width="80" height="80">
            <?php endif; ?>
            <div class="cliente-nome"><?= htmlspecialchars($row['nome_cliente']) ?></div>
        </div>

        <form action="../php/finalizar_servico.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_contratacao" value="<?= $row['id'] ?>">
            <label for="foto_servico">Foto do serviço finalizado:</label>
            <input type="file" name="foto_servico" id="foto_servico" required>
            <button type="submit" class="btn-finalizar">Finalizar Serviço</button>
        </form>
    </div>
<?php endwhile; ?>

<a href="../funcionario/painel_funcionario.php" class="btn-voltar">⬅ Voltar</a>

<script src="../js/tema.js"></script>

</body>
</html>
