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

    $input = json_decode(file_get_contents("php://input"), true);
    $nome  = $input["nome"]  ?? '';
    $email = $input["email"] ?? '';
    $senha = $input["senha"] ?? '';
    $id    = $_SESSION["id_usuario"];

    $stmt = $conn->prepare("UPDATE Usuario SET nome = ?, email = ?, senha = ? WHERE id_usuario = ?");

    if (!$stmt) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao preparar a query."]);
        exit;
    }

    $stmt->bind_param("sssi", $nome, $email, $senha, $id);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar usuário."]);
    }

    $stmt->close();
    $conn->close();
?>
