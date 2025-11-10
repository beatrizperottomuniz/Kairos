<?php
    $input = json_decode(file_get_contents("php://input"), true);

    $id_bloqueio = $input['id_bloqueio'] ?? null;
    $data_inicio = $input['data_inicio'] ?? null;
    $data_fim = $input['data_fim'] ?? null;

    if (!$id_bloqueio || !$data_inicio || !$data_fim) {
        echo json_encode(["sucesso" => false, "mensagem" => "Campos obrigatórios ausentes."]);
        exit;
    }

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }

    $sql = "UPDATE Disponibilidade_Bloqueada 
            SET data_inicio = ?, data_fim = ?
            WHERE id_bloqueio = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $data_inicio, $data_fim, $id_bloqueio);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar bloqueio."]);
    }
?>
