<?php
    header('Content-Type: application/json');
    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao conectar.']);
        exit;
    }

    if($_SERVER["REQUEST_METHOD"]=="GET"){

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT 
                A.id_agendamento,
                A.data_hora_inicio,
                A.data_hora_fim,
                A.status,
                A.observacao,
                COALESCE(A.nome_servico, S.nome_servico) AS nome_servico,
                S.descricao_geral,
                PS.preco,
                PS.duracao_minutos,
                U_Cliente.nome AS nome_cliente,
                U_Profissional.nome AS profissional,
                L.endereco AS endereco_agendamento, 
                L.CEP AS cep,
                L.tipo_local AS tipo_local,
                L.observacoes AS observacoes_local
            FROM Agendamento AS A
            JOIN Profissional_Servico AS PS ON A.id_profissional_servico = PS.id_profissional_servico
            JOIN Servico AS S ON PS.id_servico = S.id_servico
            JOIN Usuario AS U_Cliente ON A.id_cliente = U_Cliente.id_usuario
            JOIN Usuario AS U_Profissional ON PS.id_usuario_profissional = U_Profissional.id_usuario
            LEFT JOIN Local_Atendimento AS L ON L.id_profissional = PS.id_usuario_profissional
            WHERE A.id_agendamento = ?
            LIMIT 1
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
