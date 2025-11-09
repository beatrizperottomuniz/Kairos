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
    $endereco = $input['endereco'] ?? null;
    $CEP = $input['CEP'] ?? null;
    $tipo_local = $input['tipo_local'] ?? '';
    $observacoes = $input['observacoes'] ?? null;

    if (empty($id_local) || empty($tipo_local)) {
        echo json_encode(["sucesso" => false, "mensagem" => "ID do local e tipo são obrigatórios."]);
        exit;
    }

   $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE Local_Atendimento SET endereco = ?, CEP = ?, tipo_local = ?, observacoes = ? WHERE id_local = ? AND id_profissional = ?");
    $stmt->bind_param("ssssii", $endereco, $CEP, $tipo_local, $observacoes, $id_local, $id_profissional);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["sucesso" => true, "mensagem" => "Local atualizado."]);
        } else {
            echo json_encode(["sucesso" => false, "mensagem" => "Nenhum local encontrado ou dados são iguais."]);
        }
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar local: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
?>