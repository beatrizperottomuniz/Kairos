<?php
    header('Content-Type: application/json');

    $conn = mysqli_connect("localhost:3306", "root", "", "Kairos");
    if (!$conn) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão.']);
        exit();
    }

    $termo = $_GET['termo'] ?? '';

    if (empty($termo)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Termo de busca vazio.']);
        exit();
    }

    // termo para LIKE (busca parcial)
    $termo_like = "%" . $termo . "%";

    //busca em 3 colunas -> nome do profissional, especialidade e nome do Serviço
    $sql = "SELECT DISTINCT u.id_usuario, u.nome, pp.especialidade
            FROM Usuario u
            JOIN Perfil_Profissional pp ON u.id_usuario = pp.id_usuario
            LEFT JOIN Profissional_Servico ps ON u.id_usuario = ps.id_usuario_profissional
            LEFT JOIN Servico s ON ps.id_servico = s.id_servico
            WHERE u.tipo_conta = 'profissional' AND (
                u.nome LIKE ? OR
                pp.especialidade LIKE ? OR
                s.nome_servico LIKE ?
            )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $termo_like, $termo_like, $termo_like);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $profissionais = $resultado->fetch_all(mode: MYSQLI_ASSOC);

    echo json_encode(['sucesso' => true, 'dados' => $profissionais]);

    $stmt->close();
    $conn->close();
?>