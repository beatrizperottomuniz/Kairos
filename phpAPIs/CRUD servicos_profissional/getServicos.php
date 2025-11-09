<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não logado."]);
        exit;
    }
    $id_prof = $_SESSION['id_usuario'];

    $sql = "SELECT ps.id_profissional_servico, ps.id_servico, ps.preco, ps.duracao_minutos, ps.descricao_adicional, s.nome_servico 
            FROM Profissional_Servico ps
            JOIN Servico s ON ps.id_servico = s.id_servico
            WHERE ps.id_usuario_profissional = ?
            ORDER BY s.nome_servico";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_prof);
    $stmt->execute();
    $result = $stmt->get_result();
    $dados = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["sucesso" => true, "dados" => $dados]);
    $stmt->close();
    $conn->close();
?>