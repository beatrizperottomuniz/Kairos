<?php
    $input = json_decode(file_get_contents("php://input"), true);
    $id_bloqueio = $input['id_bloqueio'] ?? null;

    if (!$id_bloqueio) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID não informado."]);
        exit;
    }

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }

    $sql = "DELETE FROM Disponibilidade_Bloqueada WHERE id_bloqueio = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_bloqueio);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao excluir bloqueio."]);
    }
?>
