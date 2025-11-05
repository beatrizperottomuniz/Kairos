<?php
session_start();
header('Content-Type: application/json');

// Verifica se a sessão existe
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["erro" => "Usuário não está logado"]);
    exit;
}

// Conecta ao banco
$conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

if (!$conn) {
    echo json_encode(["erro" => "Falha na conexão com o banco."]);
    exit;
}

$id = $_SESSION['id_usuario'];
$result = mysqli_query($conn, "SELECT tipo_conta FROM usuario WHERE id_usuario = $id");

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
} else {
    echo json_encode(["erro" => "Usuário não encontrado"]);
}
?>
