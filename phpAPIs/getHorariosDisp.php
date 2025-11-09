<?php
    header('Content-Type: application/json');

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão.']);
        exit();
    }

    $id_profissional_servico = $_GET['id_profissional_servico'] ?? 0;
    $data_str = $_GET['data'] ?? ''; 

    if (empty($id_profissional_servico) || empty($data_str)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Serviço ou data não fornecidos.']);
        exit();
    }

    try {
        //descobre a duracao e o id do profissional
        $stmt = $conn->prepare("SELECT duracao_minutos, id_usuario_profissional FROM Profissional_Servico WHERE id_profissional_servico = ?");
        $stmt->bind_param("i", $id_profissional_servico);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) throw new Exception("Serviço inválido.");
        
        $servico_info = $result->fetch_assoc();
        $duracao_servico = (int)$servico_info['duracao_minutos'];
        $id_profissional = (int)$servico_info['id_usuario_profissional'];
        $stmt->close();

        //descobre o dia da semana e formatar a data
        $data_obj = new DateTime($data_str);
        $data_sql = $data_obj->format('Y-m-d');
        $dias_semana_map = ['Domingo', 'Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'Sabado'];
        $dia_semana_indice = (int)$data_obj->format('w'); // 0 = dom, 1 = seg...
        $dia_semana_nome = $dias_semana_map[$dia_semana_indice];

        //busca a disponibilidade do profissional (horario de trabalho normal)
        $stmt = $conn->prepare("SELECT hora_inicio, hora_fim FROM Disponibilidade WHERE id_usuario_profissional = ? AND dia_semana = ?");
        $stmt->bind_param("is", $id_profissional, $dia_semana_nome);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) throw new Exception("Profissional não atende neste dia da semana.");
        
        $disp_info = $result->fetch_assoc();
        $inicio_trabalho = new DateTime($data_sql . ' ' . $disp_info['hora_inicio']);
        $fim_trabalho = new DateTime($data_sql . ' ' . $disp_info['hora_fim']);
        $stmt->close();

        //busca todos os agendamentos e bloqueios do profissional no dia que quer agendar
        $ocupados = [];
        $data_inicio_dia = $data_sql . " 00:00:00";
        $data_fim_dia = $data_sql . " 23:59:59";

        //pegar agendamentos
        $sql_agend = "SELECT data_hora_inicio, data_hora_fim 
                    FROM Agendamento a
                    JOIN Profissional_Servico ps ON a.id_profissional_servico = ps.id_profissional_servico
                    WHERE ps.id_usuario_profissional = ? 
                    AND a.status IN ('Pendente', 'Confirmado')
                    AND a.data_hora_inicio BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql_agend);
        $stmt->bind_param("iss", $id_profissional, $data_inicio_dia, $data_fim_dia);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $ocupados[] = [
                'inicio' => new DateTime($row['data_hora_inicio']),
                'fim' => new DateTime($row['data_hora_fim'])
            ];
        }
        $stmt->close();

        // pegar bloqueios
        $sql_bloq = "SELECT data_inicio, data_fim FROM Disponibilidade_Bloqueada 
                    WHERE id_profissional = ? AND data_inicio <= ? AND data_fim >= ?";
        $stmt = $conn->prepare($sql_bloq);
        $stmt->bind_param("iss", $id_profissional, $data_fim_dia, $data_inicio_dia); // Pega bloqueios que *cruzam* o dia
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $ocupados[] = [
                'inicio' => new DateTime($row['data_inicio']),
                'fim' => new DateTime($row['data_fim'])
            ];
        }
        $stmt->close();

        //ver slots dispn
        $horarios_disponiveis = [];
        $intervalo = new DateInterval("PT{$duracao_servico}M");
        $slot_atual = clone $inicio_trabalho;

        while ($slot_atual < $fim_trabalho) {
            $slot_fim = (clone $slot_atual)->add($intervalo);

            // se slot terminar depois do fim do trabalho = invalido
            if ($slot_fim > $fim_trabalho) {
                break;
            }

            $conflito = false;
            foreach ($ocupados as $ocupado) {
                // verifica se o slot (slot_atual ate slot_fim) conflita com periodo ocupado (inicio ate fim)
                if ($slot_atual < $ocupado['fim'] && $slot_fim > $ocupado['inicio']) {
                    $conflito = true;
                    break;
                }
            }

            if (!$conflito) {
                $horarios_disponiveis[] = $slot_atual->format('H:i:s');
            }

            // Vai para o prox slot
            $slot_atual->add($intervalo); // pula pelo tempo de servico
        }

        echo json_encode(['sucesso' => true, 'horarios' => $horarios_disponiveis]);

    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
    }

    $conn->close();
?>