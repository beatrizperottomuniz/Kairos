<?php

header('Content-Type: application/json');

$conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
if (!$conn) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit();
}

//se post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dados = json_decode(file_get_contents(filename: 'php://input'), true);
    $nome = $dados["nome"] ?? '';
    $email = $dados["email"] ?? '';
    $senha = $dados["senha"] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: Nome, email, senha são obrigatórios.']);
        exit();
    }

    $tipoConta = 'cliente';

    try {
        $stmtUsuario = $conn->prepare("INSERT INTO Usuario (nome, email, senha, tipo_conta) VALUES (?, ?, ?, ?)");
        $stmtUsuario->bind_param("ssss", $nome, $email, $senha, $tipoConta);
        $stmtUsuario->execute();

        $id_novo_usuario = $conn->insert_id; //ID ultimo registro inserido no bd na conn
        //comeca uma sessao com esse user
        session_start();
        $_SESSION['id_usuario'] = $id_novo_usuario;
        $_SESSION['nome_usuario'] = $nome;

        echo json_encode(['sucesso' => true, 'mensagem' => 'Cliente cadastrado com sucesso!']);



    } catch (mysqli_sql_exception $exception) {
        if ($conn->errno == 1062) {//unique do email
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: Este email já está cadastrado.']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no cadastro: ' . $exception->getMessage()]);
        }
    }

}
$stmtUsuario->close();
$conn->close();
?>