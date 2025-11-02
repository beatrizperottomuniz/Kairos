<?php

// to do : senha em hash? 
header('Content-Type: application/json');

$conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
if (!$conn) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dados = json_decode(file_get_contents(filename: 'php://input'), true);
    $nome = $dados["nome"] ?? '';
    $email = $dados["email"] ?? '';
    $senha = $dados["senha"] ?? ''; 
    $especialidade = $dados["especialidade"] ?? '';
    $biografia = $dados["biografia"] ?? '';
    $endereco = $dados["endereco"] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: Nome, email, senha são obrigatórios.']);
        exit();
    }

    $tipoConta = 'profissional';

    //transacao pq vai ter insert em duas tabelas
    $conn->begin_transaction();

    try {
        //insert em usuario
        $stmtUsuario = $conn->prepare("INSERT INTO Usuario (nome, email, senha, tipo_conta) VALUES (?, ?, ?, ?)");
        $stmtUsuario->bind_param("ssss", $nome, $email, $senha, $tipoConta);
        $stmtUsuario->execute();

        $id_novo_usuario = $conn->insert_id;
        $stmtUsuario->close();

        //insert no profissional
        $stmtPerfil = $conn->prepare("INSERT INTO Perfil_Profissional (id_usuario, especialidade, biografia, endereco) VALUES (?, ?, ?, ?)");
        $stmtPerfil->bind_param("isss", $id_novo_usuario, $especialidade, $biografia, $endereco);
        $stmtPerfil->execute();
        $stmtPerfil->close();

        //comeca a sessao com esse usuario
        session_start();
        $_SESSION['id_usuario'] = $id_novo_usuario;
        $_SESSION['nome_usuario'] = $nome;

        $conn->commit();
        echo json_encode(['sucesso' => true, 'mensagem' => 'Profissional cadastrado com sucesso!']);

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();//rollback se erro 
        if ($exception->getCode() == 1062) {//-> nova operacao, e o erro fica guardado aqui na exception de php
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: Este email já está cadastrado.']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no cadastro: ' . $exception->getMessage()]);
        }
    }

} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método de requisição inválido.']);
}
$stmtPerfil->close();
$stmtUsuario->close();
$conn->close();
?>