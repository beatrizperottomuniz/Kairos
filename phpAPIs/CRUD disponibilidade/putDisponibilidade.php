<?php
    $input = json_decode(file_get_contents("php://input"), true);

    $id_disponibilidade = $input['id_disponibilidade'] ?? null;
    $dia_semana = $input['dia_semana'] ?? null;
    $hora_inicio = $input['hora_inicio'] ?? null;
    $hora_fim = $input['hora_fim'] ?? null;

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }

    if (!$id_disponibilidade || !$dia_semana || !$hora_inicio || !$hora_fim) {
        echo json_encode(["sucesso" => false, "mensagem" => "Campos obrigatórios ausentes."]);
        exit;
    }

    $sql = "UPDATE Disponibilidade 
            SET dia_semana = ?, hora_inicio = ?, hora_fim = ?
            WHERE id_disponibilidade = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $dia_semana, $hora_inicio, $hora_fim, $id_disponibilidade);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar disponibilidade."]);
    }
?>
