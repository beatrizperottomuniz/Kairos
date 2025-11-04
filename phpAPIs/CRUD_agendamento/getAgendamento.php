<?php
    header('Content-Type: application/json');
    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao conectar.']);
        exit;
    }

    if($_SERVER["REQUEST_METHOD"]=="GET"){
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido.']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT 
                A.id_agendamento,
                A.data_hora_inicio,
                A.data_hora_fim,
                A.status,
                S.nome_servico,
                PS.duracao_minutos,
                U_Cliente.nome AS nome_cliente
            FROM Agendamento AS A
            JOIN Profissional_Servico AS PS ON A.id_profissional_servico = PS.id_profissional_servico
            JOIN Servico AS S ON PS.id_servico = S.id_servico
            JOIN Usuario AS U_Cliente ON A.id_cliente = U_Cliente.id_usuario
            WHERE A.id_agendamento = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($agendamento = $result->fetch_assoc()) {
            echo json_encode(['sucesso' => true, 'agendamento' => $agendamento]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Agendamento não encontrado.']);
        }
        $stmt->close();
    }
    $conn->close();
?>
