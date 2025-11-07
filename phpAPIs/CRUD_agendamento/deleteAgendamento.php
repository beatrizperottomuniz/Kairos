<?php
    header('Content-Type: application/json');
    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao conectar.']);
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "PUT") {
        $dados = json_decode(file_get_contents("php://input"), true);
        $id = $dados["id_agendamento"] ?? null;

        if (!$id) {
            echo json_encode(["sucesso" => false, "mensagem" => "ID do agendamento não informado."]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE Agendamento SET status = 'Cancelado' WHERE id_agendamento = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["sucesso" => true]);
        } else {
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar status."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Método inválido."]);
    }

    $conn->close();
?>