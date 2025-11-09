<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não logado."]);
        exit;
    }

    $input = json_decode(file_get_contents("php://input"), true);
    $id_profissional = $_SESSION['id_usuario'];
    $id_local = $input['id_local'] ?? 0;

    if (empty($id_local)) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID do local não fornecido."]);
        exit;
    }

   $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }
    
    $stmt = $conn->prepare("DELETE FROM Local_Atendimento WHERE id_local = ? AND id_profissional = ?");
    $stmt->bind_param("ii", $id_local, $id_profissional);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["sucesso" => true, "mensagem" => "Local excluído."]);
        } else {
            echo json_encode(["sucesso" => false, "mensagem" => "Nenhum local encontrado para excluir."]);
        }
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao excluir local: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
?>