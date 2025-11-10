<?php
    $input = json_decode(file_get_contents("php://input"), true);
    $id_disponibilidade = $input['id_disponibilidade'] ?? null;

    if (!$id_disponibilidade) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID não informado."]);
        exit;
    }

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }

    $sql = "DELETE FROM Disponibilidade WHERE id_disponibilidade = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_disponibilidade);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao excluir disponibilidade."]);
    }
?>
