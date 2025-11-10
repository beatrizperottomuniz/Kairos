 <?php
    session_start();

    $input = json_decode(file_get_contents("php://input"), true);
    $id_profissional = $_SESSION['id_usuario'] ?? null;

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }

    if (!$id_profissional) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não autenticado."]);
        exit;
    }

    $dia_semana = $input['dia_semana'] ?? null;
    $hora_inicio = $input['hora_inicio'] ?? null;
    $hora_fim = $input['hora_fim'] ?? null;

    if (!$dia_semana || !$hora_inicio || !$hora_fim) {
        echo json_encode(["sucesso" => false, "mensagem" => "Campos obrigatórios ausentes."]);
        exit;
    }

    $sql = "INSERT INTO Disponibilidade (id_usuario_profissional, dia_semana, hora_inicio, hora_fim)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $id_profissional, $dia_semana, $hora_inicio, $hora_fim);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao inserir disponibilidade."]);
    }
?>
