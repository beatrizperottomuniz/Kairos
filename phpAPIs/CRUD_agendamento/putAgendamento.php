<?php
session_start();
header('Content-Type: application/json');

$conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
if (!$conn) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$id = $input['id_agendamento'] ?? null;
$status = $input['status'] ?? null;

if (!$id || !$status) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
    exit;
}

$stmt = $conn->prepare("UPDATE Agendamento SET status = ? WHERE id_agendamento = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar']);
}

$stmt->close();
$conn->close();
?>
