<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não está logado."]);
        exit;
    }

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }

    $id = $_SESSION['id_usuario'];

    $agora = date('Y-m-d H:i:s');

    $sql_cancelar_cliente = "
        UPDATE Agendamento
        SET status = 'Cancelado'
        WHERE id_cliente = $id
        AND data_hora_inicio > '$agora'
    ";

    mysqli_query($conn, $sql_cancelar_cliente);

    $sql_cancelar_profissional = "
        UPDATE Agendamento
        SET status = 'Cancelado'
        WHERE id_profissional_servico IN (
            SELECT id_profissional_servico
            FROM Profissional_Servico
            WHERE id_usuario_profissional = $id
        )
        AND data_hora_inicio > '$agora'
    ";

    mysqli_query($conn, $sql_cancelar_profissional);

    $sql_delete = "DELETE FROM Usuario WHERE id_usuario = $id";

    if (mysqli_query($conn, $sql_delete)) {
        session_unset();
        session_destroy();
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao excluir usuário."]);
    }

    mysqli_close($conn);
?>
