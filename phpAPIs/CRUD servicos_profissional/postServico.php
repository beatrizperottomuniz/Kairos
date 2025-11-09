<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não logado."]);
        exit;
    }

    $input = json_decode(file_get_contents("php://input"), true);
    $id_prof = $_SESSION['id_usuario'];

    $id_servico = $input['id_servico'];
    $preco = $input['preco'];
    $duracao = $input['duracao_minutos'];
    $desc = $input['descricao_adicional'] ?? null;

    $stmt = $conn->prepare("INSERT INTO Profissional_Servico (id_usuario_profissional, id_servico, preco, duracao_minutos, descricao_adicional) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidis", $id_prof, $id_servico, $preco, $duracao, $desc);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true, "id_inserido" => $conn->insert_id]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro (talvez este serviço já exista): " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
?>