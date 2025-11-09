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
    $preco = $input['preco'];
    $duracao = $input['duracao_minutos'];
    $desc = $input['descricao_adicional'] ?? null;

    $stmt = $conn->prepare("UPDATE Profissional_Servico SET preco = ?, duracao_minutos = ?, descricao_adicional = ? 
                            WHERE id_profissional_servico = ? AND id_usuario_profissional = ?");
    $stmt->bind_param("disii", $preco, $duracao, $desc, $id_ps, $id_prof);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ou nenhum dado alterado."]);
    }
    $stmt->close();
    $conn->close();
?>