<?php
    header('Content-Type: application/json');

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão.']);
        exit();
    }

    //recebe id de profissional
    $id_profissional = $_GET['id_profissional'] ?? 0;

    if ($id_profissional == 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID do profissional não fornecido.']);
        exit();
    }

    $sql = "SELECT 
                ps.id_profissional_servico, 
                s.nome_servico, 
                ps.preco, 
                ps.duracao_minutos
            FROM Profissional_Servico ps
            JOIN Servico s ON ps.id_servico = s.id_servico
            WHERE ps.id_usuario_profissional = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_profissional);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $servicos = $resultado->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $servicos]);

    $stmt->close();
    $conn->close();
?>