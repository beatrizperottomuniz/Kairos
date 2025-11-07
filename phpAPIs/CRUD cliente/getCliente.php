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

    $sql = "SELECT id_usuario, nome, email, tipo_conta,senha 
            FROM Usuario 
            WHERE id_usuario = $id AND tipo_conta = 'cliente'";

    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            "sucesso" => true,
            "usuario" => $row
        ]);
    } else {
        echo json_encode(["erro" => "Cliente não encontrado."]);
    }

    mysqli_close($conn);
?>
