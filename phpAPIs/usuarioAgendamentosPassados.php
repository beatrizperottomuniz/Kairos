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


    if($_SERVER["REQUEST_METHOD"]=="GET"){
        //tipo da conta (cliente ou profissional)
        $queryTipo = $conn->prepare("SELECT tipo_conta FROM Usuario WHERE id_usuario = ?");
        $queryTipo->bind_param("i", $idDoUsuarioLogado);
        $queryTipo->execute();
        $resultTipo = $queryTipo->get_result();
        $tipoConta = $resultTipo->fetch_assoc()['tipo_conta'] ?? null;
        $queryTipo->close();

        if (!$tipoConta) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado.']);
            exit();
        }

        //COALESCE = primeiro valor nao nulo
        $stmt = "SELECT
                A.id_agendamento,
                A.data_hora_inicio,
                A.data_hora_fim,          
                A.status,                 
                A.nome_servico,
                COALESCE(PS.duracao_minutos, 0) AS duracao_minutos,        
                COALESCE(U_Prof.nome, 'Profissional deletado') AS nome_profissional,
                COALESCE(U_Cliente.nome, 'Cliente deletado') AS nome_cliente
            FROM
                Agendamento AS A
            LEFT JOIN
                Profissional_Servico AS PS ON A.id_profissional_servico = PS.id_profissional_servico
            LEFT JOIN
                Servico AS S ON PS.id_servico = S.id_servico
            LEFT JOIN
                Usuario AS U_Prof ON PS.id_usuario_profissional = U_Prof.id_usuario
            LEFT JOIN
                Usuario AS U_Cliente ON A.id_cliente = U_Cliente.id_usuario
            ";

        if ($tipoConta === 'cliente') {
            $stmt .= " WHERE A.id_cliente = ?";
        } else {
            $stmt .= " WHERE PS.id_usuario_profissional  = ?";
        }
        
        $stmt .= " ORDER BY A.data_hora_inicio ASC";

        $stmt_preparado = $conn->prepare($stmt);
        $stmt_preparado->bind_param("i", $idDoUsuarioLogado);
        

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