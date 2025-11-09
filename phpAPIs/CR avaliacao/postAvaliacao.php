<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não está logado.']);
    exit;
}

$conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
if (!$conn) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dados = json_decode(file_get_contents("php://input"), true);
    $id_agendamento = $dados["id_agendamento"] ?? null;
    $nota = $dados["nota"] ?? null;
    $id_cliente = $_SESSION["id_usuario"];

    if (!$id_agendamento || !$nota) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID do agendamento e nota são obrigatórios.']);
        exit;
    }

    // ve se ja foi avaliado
    $check = $conn->prepare("SELECT id_avaliacao FROM Avaliacao WHERE id_agendamento = ?");
    $check->bind_param("i", $id_agendamento);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Este agendamento já foi avaliado.']);
        exit;
    }
    $check->close();

    // id_profissional do agendamento
    $queryProf = $conn->prepare("
        SELECT PS.id_usuario_profissional 
        FROM Agendamento A 
        JOIN Profissional_Servico PS ON A.id_profissional_servico = PS.id_profissional_servico 
        WHERE A.id_agendamento = ?
    ");
    $queryProf->bind_param("i", $id_agendamento);
    $queryProf->execute();
    $resProf = $queryProf->get_result();
    $id_profissional = $resProf->fetch_assoc()['id_usuario_profissional'] ?? null;
    $queryProf->close();

    $stmt = $conn->prepare("
        INSERT INTO Avaliacao (id_agendamento, id_cliente, id_profissional, nota, data_avaliacao)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("iiii", $id_agendamento, $id_cliente, $id_profissional, $nota);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Avaliação registrada com sucesso.']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao registrar a avaliação.']);
    }

    $stmt->close();
}

$conn->close();
?>
