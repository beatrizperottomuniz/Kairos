<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não logado."]);
        exit;
    }
    
    $input = json_decode(file_get_contents("php://input"), true);
    $id_prof = $_SESSION['id_usuario'];
    $id_ps = $input['id_profissional_servico'];

    $stmt = $conn->prepare("DELETE FROM Profissional_Servico WHERE id_profissional_servico = ? AND id_usuario_profissional = ?");
    $stmt->bind_param("ii", $id_ps, $id_prof);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ou item não encontrado."]);
    }
    $stmt->close();
    $conn->close();
?>