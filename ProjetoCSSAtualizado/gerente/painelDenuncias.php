<?php 
session_start();
include("../php/conexao.php");

// valida se gerente está logado
if (!isset($_SESSION['id_gerente'])) {
    exit("Acesso negado");
}

// busca denúncias
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
<title>Painel de Denúncias</title>
<style>
/* ======== ESTILO BASE ======== */
* {
  box-sizing: border-box;
}
body {
  background: #f3f4f6;
  font-family: 'Segoe UI', Arial, sans-serif;
  margin: 0;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* ======== CONTAINER ======== */
.container {
  background: #fff;
  width: 100%;
  max-width: 1200px;
  padding: 30px;
  border-radius: 16px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

/* ======== TÍTULO ======== */
h2 {
  color: #1a1a1a;
  font-size: 28px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 25px;
}

/* ======== TABELA ======== */
table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 10px;
  overflow: hidden;
}
th, td {
  padding: 9px;
  text-align: center;
  border-bottom: 1px solid #e0e0e0;
}
th {
  background: #ff4b2b;
  border: 1px solid #cf3d23ff;
  color: white;
  text-transform: uppercase;
  letter-spacing: 1px;
}
tr:hover {
  background: #f9f9ff;
  transition: background 0.3s ease;
}

/* ======== BOTÕES ======== */
button, select, a {
  font-family: inherit;
  font-size: 13px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

select {
  padding: 6px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

/* Banir */
button[type="submit"] {
  background: #4b6cb7;
  color: #fff;
  padding: 8px 16px;
}
button[type="submit"]:hover {
  background: #3b5ca4;
}

/* Botão Banir vermelho */
form[action="banir.php"] button {
  background: #dc3545;
}
form[action="banir.php"] button:hover {
  background: #e85c5c;
}

/* Marcar como analisada */
form[action="analisar.php"] button {
  background: #28a745;
}
form[action="analisar.php"] button:hover {
  background: #36c15a;
}

/* Ver chat */
form[action="visualizarChat.php"] button {
  background: #007bff;
}
form[action="visualizarChat.php"] button:hover {
  background: #358af4;
}

/* Excluir */
form[action="excluir_denuncia.php"] button {
  background: #c0392b;
}
form[action="excluir_denuncia.php"] button:hover {
  background: #e74c3c;
}

/* Voltar */
a.voltar, a[href="painelGerente.php"] {
  display: inline-block;
  margin-top: 20px;
  background: #ff4b2b;
  color: white;
  text-decoration: none;
  padding: 10px 20px;
  border-radius: 12px;
}
a.voltar:hover, a[href="painelGerente.php"]:hover {
  background: #cf3d23ff;
}

/* ======== BOTÃO DE TEMA ======== */
.theme-toggle {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #ddd;
  border-radius: 50%;
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}
.theme-toggle:hover {
  background: #bbb;
}

/* ======== DARK MODE ======== */
body.dark {
  background: #1e1e1e;
  color: #e0e0e0;
}
body.dark .container {
  background: #2a2a2a;
  box-shadow: 0 4px 15px rgba(255,255,255,0.1);
}
body.dark th {
  background: #1a1a1a;
}
body.dark tr:hover {
  background: #333;
}
body.dark a.voltar, body.dark a[href="painelGerente.php"] {
  background: #1a1a1a;
}
body.dark a.voltar:hover {
  background: #111111ff;
}
</style>
</head>

<body>
<button class="theme-toggle" id="theme-toggle">🌙</button>

<div class="container">
<h2>Denúncias Registradas</h2>
<table>
<tr>
    <th>ID</th>
    <th>Denunciante</th>
    <th>Denunciado</th>
    <th>Motivo</th>
    <th>Data</th>
    <th>Status</th>
    <th colspan="4">Ações</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id_denuncia'] ?></td>
    <td><?= htmlspecialchars($row['denunciante']) ?></td>
    <td><?= htmlspecialchars($row['denunciado']) ?></td>
    <td><?= htmlspecialchars($row['motivo']) ?></td>
    <td><?= date('d/m/Y H:i', strtotime($row['data_denuncia'])) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>

    <td>
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
    </td>

    <td>
        <form action="analisar.php" method="POST" style="display:inline;">
            <input type="hidden" name="id_denuncia" value="<?= $row['id_denuncia'] ?>">
            <button type="submit">Marcar como analisada</button>
        </form>
    </td>

    <td>
        <form action="visualizarChat.php" method="POST" style="display:inline;">
            <input type="hidden" name="id_denunciado" value="<?= $row['id_denunciado']?>">
            <button type="submit">Ver chat</button>
        </form>
    </td>

    <td>
        <?php if ($row['status'] === 'analisada' || $row['status'] === 'banido'): ?>
            <form action="excluir_denuncia.php" method="POST" onsubmit="return confirm('Deseja realmente excluir esta denúncia?');">
                <input type="hidden" name="id_denuncia" value="<?= $row['id_denuncia'] ?>">
                <button type="submit">Excluir</button>
            </form>
        <?php else: ?>
            <small>Disponível após ação</small>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>

<a href="painelGerente.php" class="voltar"> Voltar</a>
</div>

<script>
// Botão de alternância de tema
const btn = document.getElementById("theme-toggle");
btn.addEventListener("click", () => {
  document.body.classList.toggle("dark");
  btn.textContent = document.body.classList.contains("dark") ? "☀️" : "🌙";
});
</script>
</body>
</html>