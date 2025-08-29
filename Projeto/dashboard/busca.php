<?php
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "root", "", "sistema_usuarios");
if ($conn->connect_error) {
    die(json_encode(["erro" => "Erro na conexão: " . $conn->connect_error]));
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT id, nome, tipo, ocupacao FROM usuarios";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " WHERE tipo LIKE '%$search%' OR ocupacao LIKE '%$search%'";
}

$result = $conn->query($sql);

$usuarios = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
