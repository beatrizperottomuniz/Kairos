<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(["sucesso" => false, "mensagem" => "Usuário não logado."]);
        exit;
    }

    $id_profissional = $_SESSION['id_usuario'];

   $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");

    if (!$conn) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco."]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT * FROM Local_Atendimento WHERE id_profissional = ?");
    $stmt->bind_param("i", $id_profissional);
    $stmt->execute();
    $result = $stmt->get_result();

    $locais = [];
    while ($row = $result->fetch_assoc()) {
        $locais[] = $row;
    }

    echo json_encode(["sucesso" => true, "dados" => $locais]);

    $stmt->close();
    $conn->close();
?>