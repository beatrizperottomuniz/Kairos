<?php
//sessao
session_start();

//resposta
header('Content-Type: application/json');

// conecta ao banco de dados
$conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
if (!$conn) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //js manda por post em json por que tem senha
    $dados = json_decode(file_get_contents('php://input'), true);
    $email = $dados['email'] ?? '';
    $senha = $dados['senha'] ?? '';

    //email ou senha vazio
    if (empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Por favor, preencha o email e a senha.']);
        exit();
    }


    // prepared statement
    $stmt = $conn->prepare("SELECT id_usuario, nome, senha,tipo_conta FROM Usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if ($senha === $usuario['senha']) {
            //salva na sessao dados
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome_usuario'] = $usuario['nome'];
            
            echo json_encode(['sucesso' => true, 'mensagem' => 'Login realizado com sucesso!','tipo_conta' => $usuario['tipo_conta']]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Email ou senha incorretos.']);
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Email ou senha incorretos.']);
    }
    $stmt->close();

}

$conn->close();
?>
