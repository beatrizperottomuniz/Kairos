<?php
    session_start();
    header('Content-Type: application/json');

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão']);
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "PUT") {
        $input = json_decode(file_get_contents("php://input"), true);
        $id = $input['id_agendamento'] ?? null;
        $status = $input['status'] ?? null;
        $observacao = $input['observacao'] ?? null;

        if (!$id) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID não informado']);
            exit;
        }

        if ($status) {
            $stmt = $conn->prepare("UPDATE Agendamento SET status = ? WHERE id_agendamento = ?");
            $stmt->bind_param("si", $status, $id);
        } elseif ($observacao) {
            $stmt = $conn->prepare("UPDATE Agendamento SET observacao = ? WHERE id_agendamento = ?");
            $stmt->bind_param("si", $observacao, $id);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum campo válido para atualizar']);
            exit;
        }

        if ($stmt->execute()) {
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar']);
        }

        $stmt->close();
    }
    $conn->close();
?>
