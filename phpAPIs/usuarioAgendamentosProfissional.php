<?php
    session_start();

    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não está logado.']);
        exit(); 
    }

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
        exit();
    }

    $idDoUsuarioLogado = $_SESSION['id_usuario'];

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $dataFiltro = $_GET['data'] ?? null;

        $stmt = "SELECT
                    A.id_agendamento,
                    A.data_hora_inicio,
                    A.data_hora_fim,
                    A.status,
                    S.nome_servico,
                    PS.duracao_minutos,
                    U_Cliente.nome AS nome_cliente
                FROM
                    Agendamento AS A
                JOIN
                    Profissional_Servico AS PS ON A.id_profissional_servico = PS.id_profissional_servico
                JOIN
                    Servico AS S ON PS.id_servico = S.id_servico
                JOIN
                    Usuario AS U_Cliente ON A.id_cliente = U_Cliente.id_usuario
                WHERE
                    PS.id_usuario_profissional = ? AND A.status != 'Cancelado'";
        
        if ($dataFiltro) {
            $stmt .= " AND DATE(A.data_hora_inicio) = ?";
            $stmt .= " ORDER BY A.data_hora_inicio ASC";

            $stmt_preparado = $conn->prepare($stmt);
            $stmt_preparado->bind_param("is", $idDoUsuarioLogado, $dataFiltro);
        } else {
            $stmt .= " ORDER BY A.data_hora_inicio ASC";

            $stmt_preparado = $conn->prepare($stmt);
            $stmt_preparado->bind_param("i", $idDoUsuarioLogado);
        }

        $stmt_preparado->execute();
        $resultado = $stmt_preparado->get_result();

        $agendamentos = [];

        while ($linha = $resultado->fetch_assoc()) {
            $agendamentos[] = $linha;
        }

        echo json_encode(['sucesso' => true, 'agendamentos' => $agendamentos]);

        $stmt_preparado->close();
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
    }

    $conn->close();
?>
