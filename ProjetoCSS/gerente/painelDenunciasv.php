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
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #ff4b2b;
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background: #ff4b2b;
            color: #fff;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        tr:hover {
            background: #ffe5e0;
        }

        select, button {
            padding: 6px 10px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        select {
            border: 1px solid #ccc;
        }

        button {
            background: #ff4b2b;
            color: #fff;
            margin-top: 4px;
            transition: background 0.3s;
        }

        button:hover {
            background: #e94427;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background: #ff4b2b;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .back-link:hover {
            background: #e94427;
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 0.85rem;
                padding: 8px;
            }
            select, button {
                font-size: 0.8rem;
                padding: 5px;
            }
        }
    </style>
</head>
<body>

    <h2>📋 Denúncias Registradas</h2>

    <div class="table-container">
        <table>
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
                <td class="actions">
                    <!-- Banir -->
                    <form action="banir.php" method="POST">
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
                    <form action="analisar.php" method="POST">
                        <input type="hidden" name="id_denuncia" value="<?= $row['id_denuncia'] ?>">
                        <button type="submit">Marcar como analisada</button>
                    </form>

                    <!-- Desbanir -->
                    <form action="desbanir.php" method="POST">
                        <input type="hidden" name="id_denunciado" value="<?= $row['id_denunciado'] ?>">
                        <button type="submit">Desbanir</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div style="text-align:center;">
        <a href="painelGerente.php" class="back-link">⬅ Voltar</a>
    </div>

</body>
</html>
