<?php
    header('Content-Type: application/json');
    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao conectar.']);
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id_profissional = $_GET['id_profissional'] ?? null;

        if (!$id_profissional) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID do profissional não fornecido.']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT 
                U.id_usuario AS id_profissional,
                U.nome AS nome_profissional,
                U.email AS email_profissional,
                U.telefone,
                U.especialidade,
                S.id_servico,
                S.nome_servico,
                S.descricao_geral,
                PS.id_profissional_servico,
                PS.preco,
                PS.duracao_minutos
            FROM Profissional_Servico AS PS
            JOIN Servico AS S ON PS.id_servico = S.id_servico
            JOIN Usuario AS U ON PS.id_profissional = U.id_usuario
            WHERE PS.id_profissional = ?
        ");
        $stmt->bind_param("i", $id_profissional);
        $stmt->execute();
        $result = $stmt->get_result();

        $dados = [];
        while ($row = $result->fetch_assoc()) {
            $dados[] = [
                'profissional' => [
                    'id_profissional' => $row['id_profissional'],
                    'nome' => $row['nome_profissional'],
                    'email' => $row['email_profissional'],
                    'telefone' => $row['telefone'],
                    'especialidade' => $row['especialidade']
                ],
                'servico' => [
                    'id_servico' => $row['id_servico'],
                    'nome_servico' => $row['nome_servico'],
                    'descricao_geral' => $row['descricao_geral']
                ],
                'profissional_servico' => [
                    'id_profissional_servico' => $row['id_profissional_servico'],
                    'preco' => $row['preco'],
                    'duracao_minutos' => $row['duracao_minutos']
                ]
            ];
        }

        if (count($dados) > 0) {
            echo json_encode(['sucesso' => true, 'dados' => $dados]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum serviço encontrado para este profissional.']);
        }

        $stmt->close();
    }

    $conn->close();
?>
