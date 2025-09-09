<?php

require_once("../tcpdf/tcpdf.php");

// Conexão com banco de dados
$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Captura filtro
$filtro = isset($_GET['tipo']) ? trim($_GET['tipo']) : "";
$listarTodos = isset($_GET['listar']) ? true : false;

if ($filtro !== "") {
    $sql  = "SELECT nome, email, tipo, ocupacao FROM usuarios WHERE tipo LIKE ? OR ocupacao LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = "%{$filtro}%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    if ($listarTodos) {
        $result = $conn->query("SELECT nome, email, tipo, ocupacao FROM usuarios");
    } else {
        $result = false;
    }
}

// Monta tabela em HTML para o PDF
$html = '<h2 style="text-align:center;"> Relatório de Usuários</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" style="width:100%; text-align:center;">
            <thead>
              <tr style="background-color:#ff4b2b; color:#fff;">
                <th><b>Nome</b></th>
                <th><b>Email</b></th>
                <th><b>Tipo</b></th>
                <th><b>Ocupação</b></th>
              </tr>
            </thead>
            <tbody>';

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>'.htmlspecialchars($row["nome"]).'</td>
                    <td>'.htmlspecialchars($row["email"]).'</td>
                    <td>'.htmlspecialchars($row["tipo"]).'</td>
                    <td>'.htmlspecialchars($row["ocupacao"]).'</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="4">Nenhum usuário encontrado.</td></tr>';
}

$html .= '</tbody></table>';

// Instancia o TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Sistema Usuários");
$pdf->SetTitle("Relatório de Usuários");
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// Escreve o HTML no PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Força download
$pdf->Output("relatorio_usuarios.pdf", "I");

$conn->close();
?>
