<?php  
session_start();  
include("../php/conexao.php");  

if (!isset($_SESSION['id_gerente'])) {  
    exit("Acesso negado");  
}  

$sql = "SELECT h.*, g.nome AS nome_gerente, d.id_denunciado, d.motivo   
        FROM historico_acoes_gerente h  
        JOIN gerentes g ON h.id_gerente = g.id_gerente  
        LEFT JOIN denuncias d ON h.id_denuncia = d.id_denuncia  
        ORDER BY h.data_acao DESC";  
$result = $conn->query($sql);  
?>  

<!DOCTYPE html>  
<html lang="pt-br">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>Histórico de Ações</title>  

<style>
*{box-sizing:border-box;margin:0;padding:0;}
body {
  background:#f6f5f7;
  font-family:'Arial',sans-serif;
  display:flex;
  flex-direction:column;
  align-items:center;
  min-height:100vh;
}
.nav {
  width:100%;
  background:linear-gradient(to right,#ff4b2b,#ff416c);
  color:#fff;
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:1rem 2rem;
  box-shadow:0 4px 10px rgba(0,0,0,0.2);
}
.nav h2 {color:#fff;font-size:22px;}
.nav a {
  color:#fff;
  text-decoration:none;
  padding:8px 14px;
  border-radius:20px;
  transition:.3s;
}
.nav a:hover {background:rgba(255,255,255,0.2);}
.main {
  background:#fff;
  border-radius:15px;
  box-shadow:0 10px 20px rgba(0,0,0,0.15);
  width:90%;
  max-width:1000px;
  margin-top:40px;
  padding:30px;
  text-align:center;
}
h2 {color:#1a202c;margin-bottom:20px;}
table {
  width:100%;
  border-collapse:collapse;
  margin-bottom:20px;
}
th, td {
  border:1px solid #fff;
  padding:10px;
  text-align:center;
}
th {
  background:linear-gradient(to right,#ff4b2b,#ff416c);
  color: #fff;
}
tr:nth-child(even){background: #f9f9f9;}
.voltar {
  display:inline-block;
  padding:10px 18px;
  background:#ff4b2b;
  color:#fff;
  border-radius:25px;
  text-decoration:none;
  transition:.3s;
}
.voltar:hover {background:#ff6b4b;}
body.dark {background: #1e1e1e;color: #fff;}
body.dark .main {background: #3d3d3dff;color:black;}
body.dark tr{background: #646464ff;}
body.dark th {background: #646464ff;} /* */
.theme-toggle {
  background:rgba(255,255,255,0.2);
  border:none;
  border-radius:50%;
  width:40px;height:40px;
  color: #fff;
  font-size:18px;
  cursor:pointer;
  transition:.3s;
}
.theme-toggle:hover {background:rgba(255,255,255,0.4);}
@media(max-width:700px){
  table{font-size:14px;}
  th,td{padding:6px;}
}
</style>
</head>

<body>
<nav class="nav">
  <h2>Histórico de Ações</h2>
  <div>
    <button class="theme-toggle" id="theme-toggle">🌙</button>
    <a href="painelGerente.php">Voltar</a>
  </div>
</nav>

<div class="main">
  <h2>Histórico de Ações dos Gerentes</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Gerente</th>
      <th>Denúncia</th>
      <th>Ação</th>
      <th>Detalhes</th>
      <th>Data</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>  
    <tr>  
      <td><?= $row['id'] ?></td>  
      <td><?= htmlspecialchars($row['nome_gerente']) ?></td>  
      <td><?= htmlspecialchars($row['id_denuncia'] ?? '-') ?></td>  
      <td><?= htmlspecialchars($row['acao']) ?></td>  
      <td><?= htmlspecialchars($row['detalhes']) ?></td>  
      <td><?= date('d/m/Y H:i', strtotime($row['data_acao'])) ?></td>  
    </tr>  
    <?php endwhile; ?>  
  </table>  
  <a href="painelDenuncias.php" class="voltar">Ver Denuncias</a>  
</div>

<script>
const btnTheme = document.getElementById('theme-toggle');
btnTheme.addEventListener('click',()=>{
  document.body.classList.toggle('dark');
  btnTheme.textContent=document.body.classList.contains('dark')?'☀️':'🌙';
});
</script>
</body>
</html>