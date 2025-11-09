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
    $especialidade = $input["especialidade"] ?? null;
    $biografia     = $input["biografia"]     ?? null;


    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(["sucesso" => false, "mensagem" => "Nome e email e senha são obrigatórios."]);
        $conn->close();
        exit;
    }

    $stmt = $conn->prepare("UPDATE Usuario SET nome = ?, email = ?, senha = ? WHERE id_usuario = ?");

    if (!$stmt) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao preparar a query."]);
        exit;
    }

    $stmt->bind_param("sssi", $nome, $email, $senha, $id);

    $stmt2 = $conn->prepare("UPDATE Perfil_Profissional SET especialidade = ?, biografia = ? WHERE id_usuario = ?");

    if (!$stmt2) {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao preparar a query."]);
        exit;
    }

    $stmt2->bind_param("ssi", $especialidade, $biografia, $id);

    $result = $stmt->execute();
    $result2  = $stmt2->execute();

    if ($result && $result2) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar usuário ou perfil profissional."]);
    }
    $stmt->close();
    $conn->close();
?>


