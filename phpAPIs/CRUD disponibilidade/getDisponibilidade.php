<?php
    session_start();

    $id_profissional = $_SESSION['id_usuario'] ?? null;

    if (!$id_profissional) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não autenticado."]);
        exit;
    }

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }
    
    $sql = "SELECT id_disponibilidade, dia_semana, hora_inicio, hora_fim
            FROM Disponibilidade
            WHERE id_usuario_profissional = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_profissional);
    $stmt->execute();
    $result = $stmt->get_result();

    $dados = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["sucesso" => true, "dados" => $dados]);
?>
