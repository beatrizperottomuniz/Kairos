<?php
    session_start();

    if (!isset($_SESSION['id_usuario'])) {
        die("Erro: Usuário não está logado.");
    }

    $conn = mysqli_connect("localhost", "root", "", "Kairos");
    if (!$conn) {
        die("Erro de conexão com o banco de dados: " . mysqli_connect_error());
    }

    $idDoUsuarioLogado = $_SESSION['id_usuario'];

    $sql = "
    SELECT 
        C.nome AS cliente,
        U.nome AS profissional,
        A.data_hora_inicio,
        A.data_hora_fim,
        A.status,
        A.observacao,
        S.nome_servico,
        S.descricao_geral,
        PS.preco
    FROM Agendamento A
    JOIN Usuario C ON A.id_cliente = C.id_usuario
    JOIN Profissional_Servico PS ON A.id_profissional_servico = PS.id_profissional_servico
    JOIN Usuario U ON PS.id_usuario_profissional = U.id_usuario
    JOIN Servico S ON PS.id_servico = S.id_servico
    WHERE A.id_cliente = ? OR U.id_usuario = ?
    ORDER BY A.data_hora_inicio ASC
    ";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idDoUsuarioLogado, $idDoUsuarioLogado);
    $stmt->execute();
    $resultado = $stmt->get_result();

    //cabecalho do CSV para forçar download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="agendamentos.csv"');

    //abre saida do navegador
    $output = fopen('php://output', 'w');

    //cabecalho do CSV
    fputcsv($output, [
        'cliente',
        'profissional',
        'data_hora_inicio',
        'data_hora_fim',
        'status',
        'observacao',
        'nome_servico',
        'descricao_geral',
        'preço'
    ]);

    //linhas de dados
    while ($linha = $resultado->fetch_assoc()) {
        fputcsv($output, [
            $linha['cliente'],
            $linha['profissional'],
            $linha['data_hora_inicio'],
            $linha['data_hora_fim'],
            $linha['status'],
            $linha['observacao'],
            $linha['nome_servico'],
            $linha['descricao_geral'],
            $linha['preco']
        ]);
    }
    $stmt->close();
    $conn->close();
    fclose($output);
    exit;
?>
