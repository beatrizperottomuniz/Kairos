<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não logado."]);
        exit;
    }

    $input = json_decode(file_get_contents("php://input"), true);
    $id_profissional = $_SESSION['id_usuario'];

    $endereco = $input['endereco'] ?? null;
    $CEP = $input['CEP'] ?? null;
    $tipo_local = $input['tipo_local'] ?? '';
    $observacoes = $input['observacoes'] ?? null;

    if (empty($tipo_local)) {
        echo json_encode(["sucesso" => false, "mensagem" => "O tipo de local é obrigatório."]);
        exit;
    }

   $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO Local_Atendimento (id_profissional, endereco, CEP, tipo_local, observacoes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_profissional, $endereco, $CEP, $tipo_local, $observacoes);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true, "id_inserido" => $conn->insert_id]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao adicionar local: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
?>