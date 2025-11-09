<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(["erro" => "Usuário não está logado"]);
        exit;
    }

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["erro" => "Falha na conexão com o banco."]);
        exit;
    }

    $id = $_SESSION['id_usuario'];

    $sql = "
        SELECT 
            U.id_usuario AS id_profissional,
            U.nome AS nome_profissional,
            U.email AS email_profissional,
            U.senha AS senha,
            PP.especialidade,
            PP.biografia,
            S.id_servico,
            S.nome_servico,
            S.descricao_geral,
            PS.id_profissional_servico,
            PS.preco,
            PS.duracao_minutos,
            PS.descricao_adicional,
            IFNULL(ROUND(AVG(A.nota), 2), 0) AS media_avaliacoes,
            L.endereco,
            L.CEP,
            L.tipo_local,
            L.observacoes AS observacoes_local,
            GROUP_CONCAT(
                DISTINCT CONCAT(D.dia_semana, ' ', D.hora_inicio, '-', D.hora_fim)
                ORDER BY FIELD(D.dia_semana, 'Domingo','Segunda','Terca','Quarta','Quinta','Sexta','Sabado')
                SEPARATOR '; '
            ) AS disponibilidades,
            GROUP_CONCAT(
                DISTINCT CONCAT(DB.data_inicio, ' até ', DB.data_fim)
                SEPARATOR '; '
            ) AS disponibilidades_bloqueadas
        FROM Usuario AS U
        JOIN Perfil_Profissional AS PP ON PP.id_usuario = U.id_usuario
        JOIN Profissional_Servico AS PS ON PS.id_usuario_profissional = U.id_usuario
        JOIN Servico AS S ON PS.id_servico = S.id_servico
        LEFT JOIN Avaliacao AS A ON A.id_profissional = U.id_usuario
        LEFT JOIN Local_Atendimento AS L ON L.id_profissional = U.id_usuario
        LEFT JOIN Disponibilidade AS D ON D.id_usuario_profissional = U.id_usuario
        LEFT JOIN Disponibilidade_Bloqueada AS DB ON DB.id_profissional = U.id_usuario
        WHERE U.id_usuario = $id
        GROUP BY 
            U.id_usuario, U.nome, U.email, PP.especialidade, PP.biografia,
            S.id_servico, S.nome_servico, S.descricao_geral, PS.id_profissional_servico, PS.preco, PS.duracao_minutos,PS.descricao_adicional,
            L.endereco, L.CEP, L.tipo_local, L.observacoes
    ";

    $result = mysqli_query($conn, $sql);
    $dados = [];

    while ($row = $result->fetch_assoc()) {
        $dados[] = [
            'profissional' => [
                'id_profissional' => $row['id_profissional'],
                'nome' => $row['nome_profissional'],
                'email' => $row['email_profissional'],
                'senha' => $row['senha'],
                'especialidade' => $row['especialidade'],
                'biografia' => $row['biografia'],
                'media_avaliacoes' => $row['media_avaliacoes']
            ],
            'servico' => [
                'id_servico' => $row['id_servico'],
                'nome_servico' => $row['nome_servico'],
                'descricao_geral' => $row['descricao_geral']
            ],
            'profissional_servico' => [
                'id_profissional_servico' => $row['id_profissional_servico'],
                'preco' => $row['preco'],
                'duracao_minutos' => $row['duracao_minutos'],
                'descricao_adicional' => $row['descricao_adicional']
            ],
            'local_atendimento' => [
                'endereco' => $row['endereco'],
                'CEP' => $row['CEP'],
                'tipo_local' => $row['tipo_local'],
                'observacoes' => $row['observacoes_local']
            ],
            'disponibilidades' => explode('; ', $row['disponibilidades'] ?? ''),
            'bloqueios' => explode('; ', $row['disponibilidades_bloqueadas'] ?? '')
        ];
    }

    if (count($dados) > 0) {
        echo json_encode(['sucesso' => true, 'dados' => $dados]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum serviço encontrado para este profissional.']);
    }

    $conn->close();
?>
