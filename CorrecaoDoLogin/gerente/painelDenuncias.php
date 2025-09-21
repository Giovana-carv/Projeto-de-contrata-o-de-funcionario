<?php
session_start();
include("../php/conexao.php");

// valida se gerente está logado
if (!isset($_SESSION['id_gerente'])) {
    exit("Acesso negado");
}

$sql = "SELECT d.id_denuncia, d.motivo, d.data_denuncia, d.status,
               u1.nome AS denunciante, u2.nome AS denunciado, u2.id AS id_denunciado
        FROM denuncias d
        JOIN usuarios u1 ON d.id_denunciante = u1.id
        JOIN usuarios u2 ON d.id_denunciado = u2.id
        ORDER BY d.data_denuncia DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denúncias</title>
    <style>
    table{
        background-color: white;
    }
    a{
        text-decoration: none;
        color: white;
        border: 1px solid navy;
        border-radius: 8px;
        background-color: navy;
    }
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
    body h2{
        color: black;
        text-align: center;
    }
    body.dark h2{
        color: white;
        text-align: center;
    }
    body.dark .card {
      background:#2a2a2a;
      color:#ddd;
    }
    </style>
</head>
<body>
        <button class="theme-toggle" id="theme-toggle">🌙</button>
<div class="container">
    <h2>Denúncias registradas</h2>
    <table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Denunciante</th>
        <th>Denunciado</th>
        <th>Motivo</th>
        <th>Data</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id_denuncia'] ?></td>
        <td><?= htmlspecialchars($row['denunciante']) ?></td>
        <td><?= htmlspecialchars($row['denunciado']) ?></td>
        <td><?= htmlspecialchars($row['motivo']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($row['data_denuncia'])) ?></td>
        <td><?= $row['status'] ?></td>
        <td>
            <!-- Formulário de banimento -->
            <form action="banir.php" method="POST" style="display:inline;">
                <input type="hidden" name="id_usuario" value="<?= $row['id_denunciado'] ?>">
                <input type="hidden" name="id_denuncia" value="<?= $row['id_denuncia'] ?>">
                <select name="tempo">
                    <option value="1">1 dia</option>
                    <option value="7">7 dias</option>
                    <option value="30">30 dias</option>
                    <option value="0">Permanente</option>
                </select>
                <button type="submit">Banir</button>
            </form>

            <!-- Marcar como analisada -->
            <form action="analisar.php" method="POST" style="display:inline;">
                <input type="hidden" name="id_denuncia" value="<?= $row['id_denuncia'] ?>">
                <button type="submit">Marcar como analisada</button>
            </form>
        </td>
        <td>
            <form action='desbanir.php' method='POST'>
                <input type='hidden' name='id_banimento' value='" . $row['id_banimento'] . "'>
                <button type='submit'>Desbanir</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="painelGerente.php">Voltar</a>
</div>

    <script src="../js/tema.js"></script>
</body>
</html>