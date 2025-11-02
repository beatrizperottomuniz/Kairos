<?php
//falta scape de html
    session_start();

    if (!isset($_SESSION['id_usuario'])) {
        die("Erro: Usuário não está logado."); 
    }


    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
        exit();
    }
    $idDoUsuarioLogado = $_SESSION['id_usuario'];
    $nomeDoUsuarioLogado = $_SESSION['nome_usuario'];


    if($_SERVER["REQUEST_METHOD"]=="GET"){

        $sql = "SELECT
                A.id_agendamento,
                A.data_hora_inicio,
                S.nome_servico,
                U_Prof.nome AS nome_profissional
            FROM
                Agendamento AS A
            JOIN
                Profissional_Servico AS PS ON A.id_profissional_servico = PS.id_profissional_servico
            JOIN
                Servico AS S ON PS.id_servico = S.id_servico
            JOIN
                Usuario AS U_Prof ON PS.id_usuario_profissional = U_Prof.id_usuario
            WHERE
                A.id_cliente = ? AND A.status != 'Cancelado'
            ORDER BY
                A.data_hora_inicio ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idDoUsuarioLogado);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $agendamentos = [];

        while ($linha = $resultado->fetch_assoc()) {
            $agendamentos[] = $linha;
        }

        echo json_encode(['sucesso' => true, 'agendamentos' => $agendamentos]);
    }

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $dataHoraInicioStr = $_POST['hora_dia'] ?? '';

        //para teste
        $idProfissionalServico = 1;

        if (empty($dataHoraInicioStr)) {
            exit();
        }

        //profissional oferece este serviço e qual a duracao
        $stmt = $conn->prepare("SELECT duracao_minutos, id_usuario_profissional FROM Profissional_Servico WHERE id_profissional_servico = ?");
        $stmt->bind_param("i", $idProfissionalServico);
        $stmt->execute();
        $servicoInfo = $stmt->get_result()->fetch_assoc();
        $duracao = $servicoInfo['duracao_minutos'];
        $idProfissional = $servicoInfo['id_usuario_profissional'];

        //prepara as datas e horas para valid.
        $dataHoraInicio = new DateTime($dataHoraInicioStr);
        $dataHoraFim = (new DateTime($dataHoraInicioStr))->add(new DateInterval('PT' . $duracao . 'M'));
        $diaDaSemanaMapa = ['Domingo', 'Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'Sabado'];
        $diaDaSemana = $diaDaSemanaMapa[$dataHoraInicio->format('w')];
        $horaInicio = $dataHoraInicio->format('H:i:s');

        //checa disp
        $stmtDisp = $conn->prepare("SELECT * FROM Disponibilidade WHERE id_usuario_profissional = ? AND dia_semana = ? AND ? BETWEEN hora_inicio AND hora_fim");
        $stmtDisp->bind_param("iss", $idProfissional, $diaDaSemana, $horaInicio);
        $stmtDisp->execute();
        if ($stmtDisp->get_result()->num_rows === 0) {
            header("Location: ../telaPrincipal/index.html?erro=indisponivel");
            exit();
        }

        //ja tem agend. nessa hr com profiss?
        $stmtConf = $conn->prepare("SELECT id_agendamento FROM Agendamento AS A JOIN Profissional_Servico AS PS ON A.id_profissional_servico = PS.id_profissional_servico WHERE PS.id_usuario_profissional = ? AND A.status != 'Cancelado' AND (? < A.data_hora_fim AND ? > A.data_hora_inicio)");
        $inicioStr = $dataHoraInicio->format('Y-m-d H:i:s');
        $fimStr = $dataHoraFim->format('Y-m-d H:i:s');
        $stmtConf->bind_param("iss", $idProfissional, $inicioStr, $fimStr);
        $stmtConf->execute();
        if ($stmtConf->get_result()->num_rows > 0) {
            header("Location: ../telaPrincipal/index.html?erro=conflito");
            exit();
        }

        //insere
        $sqlInsert = "INSERT INTO Agendamento (id_cliente, id_profissional_servico, data_hora_inicio, data_hora_fim) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iiss", $idDoUsuarioLogado, $idProfissionalServico, $inicioStr, $fimStr);

        if ($stmtInsert->execute()) {
            header("Location: ../telaPrincipal/index.html?status=sucesso");
        } else {
            header("Location: ../telaPrincipal/index.html?erro=inesperado");
        }


        }
    $stmtDisp->close();
    $stmtConf->close();
    $stmtInsert->close();
    $stmt->close();
    $conn->close();
?>


