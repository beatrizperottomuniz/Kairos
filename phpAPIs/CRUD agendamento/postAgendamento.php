<?php
    session_start();
    header('Content-Type: application/json');


    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
        exit();
    }

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: Usuário não autenticado. Faça login para agendar.']);
        $conn->close();
        exit();
    }

    $id_cliente = $_SESSION['id_usuario'];


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $dados = json_decode(file_get_contents('php://input'), true);

        $id_profissional_servico = $dados["id_profissional_servico"] ?? null;
        $data_hora_inicio_str = $dados["data_hora_inicio"] ?? null;
        $observacao = $dados["observacao"] ?? null; // opcional

        if (empty($id_profissional_servico) || empty($data_hora_inicio_str)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: Serviço e data/hora de início são obrigatórios.']);
            $conn->close();
            exit();
        }

        try {
            //pega duracao e nome servico
            $sql_servico = "SELECT ps.duracao_minutos, s.nome_servico 
                            FROM Profissional_Servico ps
                            JOIN Servico s ON ps.id_servico = s.id_servico
                            WHERE ps.id_profissional_servico = ?";
            
            $stmt_servico = $conn->prepare($sql_servico);
            $stmt_servico->bind_param("i", $id_profissional_servico);
            $stmt_servico->execute();
            $resultado_servico = $stmt_servico->get_result();

            if ($resultado_servico->num_rows == 0) {
                throw new Exception("Serviço selecionado é inválido.");
            }

            $servico_info = $resultado_servico->fetch_assoc();
            $duracao_minutos = (int)$servico_info['duracao_minutos'];
            $nome_servico = $servico_info['nome_servico'];
            $stmt_servico->close();


            // calcula hr final
            $data_hora_inicio = new DateTime($data_hora_inicio_str);
            
            $data_hora_fim = clone $data_hora_inicio;
            $data_hora_fim->add(new DateInterval("PT{$duracao_minutos}M")); // "PT...M" = formato para periodo de tempo em min
            
            $data_hora_inicio_sql = $data_hora_inicio->format('Y-m-d H:i:s');
            $data_hora_fim_sql = $data_hora_fim->format('Y-m-d H:i:s');
            
            $status_padrao = 'Pendente';

            $sql_insert = "INSERT INTO Agendamento 
                        (id_cliente, id_profissional_servico, nome_servico, data_hora_inicio, data_hora_fim, status, observacao) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param(
                "iisssss", 
                $id_cliente,
                $id_profissional_servico,
                $nome_servico,
                $data_hora_inicio_sql,
                $data_hora_fim_sql,
                $status_padrao,
                $observacao
            );
            
            $stmt_insert->execute();
            $stmt_insert->close();

            echo json_encode(['sucesso' => true, 'mensagem' => 'Agendamento realizado com sucesso!']);

        } catch (Exception $exception) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao processar agendamento: ' . $exception->getMessage()]);
        }

    }
    $conn->close();
?>