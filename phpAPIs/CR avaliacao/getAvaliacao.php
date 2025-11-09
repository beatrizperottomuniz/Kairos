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

    $id = $_GET['id_agendamento'] ?? null;
    if (!$id) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido']);
        exit;
    }

    $stmt = $conn->prepare("SELECT nota, id_avaliacao FROM Avaliacao WHERE id_agendamento = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        echo json_encode([
            "sucesso" => true,
            "id_avaliacao" => $row['id_avaliacao'],
            "nota" => $row['nota']
        ]);
    } else {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Nenhuma avaliação encontrada para este agendamento."
        ]);
    }

    $stmt->close();
    $conn->close();
?>
