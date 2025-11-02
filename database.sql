
DROP DATABASE IF EXISTS Kairos;

CREATE DATABASE Kairos;

USE Kairos;

CREATE TABLE Usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo_conta ENUM('cliente', 'profissional') NOT NULL
);

CREATE TABLE Perfil_Profissional (
    id_usuario INT PRIMARY KEY,
    especialidade VARCHAR(100),
    biografia VARCHAR(400),
    endereco VARCHAR(255),

    CONSTRAINT fk_perfil_usuario
        FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE 
);

CREATE TABLE Servico (
    id_servico INT PRIMARY KEY AUTO_INCREMENT,
    nome_servico VARCHAR(100) NOT NULL,
    descricao_geral TEXT
);

CREATE TABLE Profissional_Servico (
    id_profissional_servico INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario_profissional INT NOT NULL,
    id_servico INT NOT NULL,
    preco DECIMAL(10, 2) NOT NULL, 
    duracao_minutos INT UNSIGNED NOT NULL,
    descricao_adicional VARCHAR(400),

    CONSTRAINT fk_ps_profissional
        FOREIGN KEY (id_usuario_profissional) REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE,
    CONSTRAINT fk_ps_servico
        FOREIGN KEY (id_servico) REFERENCES Servico(id_servico)
        ON DELETE CASCADE, 

    UNIQUE (id_usuario_profissional, id_servico)
);

CREATE TABLE Disponibilidade (
    id_disponibilidade INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario_profissional INT NOT NULL,
    dia_semana ENUM('Domingo', 'Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'Sabado') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,

    CONSTRAINT fk_disp_profissional
        FOREIGN KEY (id_usuario_profissional) REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE 
);

CREATE TABLE Agendamento (
    id_agendamento INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_profissional_servico INT NOT NULL,
    data_hora_inicio DATETIME NOT NULL,
    data_hora_fim DATETIME NOT NULL,
    status ENUM('Pendente', 'Confirmado', 'Cancelado', 'Concluido') NOT NULL DEFAULT 'Pendente',

    CONSTRAINT fk_agend_cliente
        FOREIGN KEY (id_cliente) REFERENCES Usuario(id_usuario),
    CONSTRAINT fk_agend_prof_servico
        FOREIGN KEY (id_profissional_servico) REFERENCES Profissional_Servico(id_profissional_servico)
);


-- INSERINDO DADOS
INSERT INTO Usuario (nome, email, senha, tipo_conta) VALUES
('Ana Silva', 'ana.silva@example.com', 'senha1', 'profissional'),
('Bruno Costa', 'bruno.costa@example.com', 'senha2', 'profissional'),
('Carla Dias', 'carla.dias@example.com', 'senha3', 'profissional'),
('Daniel Faria', 'daniel.faria@example.com', 'senha4', 'profissional'),
('Eliana Santos', 'eliana.santos@example.com', 'senha5', 'profissional'),
('Fernando Lima', 'fernando.lima@example.com', 'senha6', 'cliente'),
('Gabriela Mota', 'gabriela.mota@example.com', 'senha7', 'cliente');

INSERT INTO Perfil_Profissional (id_usuario, especialidade, biografia, endereco) VALUES
(1, 'Cabeleireira', 'Especialista em cortes modernos e coloração. Mais de 10 anos de experiência.', 'Rua das Flores, 123, Sala 101'),
(2, 'Massoterapeuta', 'Focado em massagens terapêuticas e de relaxamento profundo.', 'Avenida Central, 456, Clínica Bem-Estar'),
(3, 'Esteticista', 'Especialista em tratamentos faciais, incluindo limpeza de pele e peelings.', 'Rua da Paz, 789, Espaço Beleza'),
(4, 'Barbeiro', 'Mestre em cortes clássicos e modernos, além de cuidados com a barba.', 'Travessa do Comércio, 10, Barbearia Nobre'),
(5, 'Manicure e Pedicure', 'Técnicas avançadas em cuidados com unhas, incluindo unhas de gel e spa dos pés.', 'Rua das Flores, 123, Sala 102');

INSERT INTO Servico (nome_servico, descricao_geral) VALUES
('Corte de Cabelo Feminino', 'Corte, lavagem e finalização para todos os tipos de cabelo.'),
('Manicure e Pedicure Completa', 'Cutilagem, esmaltação e hidratação para mãos e pés.'),
('Massagem Relaxante', 'Sessão de 60 minutos para alívio de tensões musculares e estresse.'),
('Limpeza de Pele Profunda', 'Extração de cravos, aplicação de máscaras e hidratação facial.'),
('Corte e Barba Terapia', 'Corte de cabelo masculino e tratamento completo para a barba com toalhas quentes.');

INSERT INTO Profissional_Servico (id_usuario_profissional, id_servico, preco, duracao_minutos, descricao_adicional) VALUES
(1, 1, 120.00, 60, 'Finalização com escova inclusa.'),
(2, 3, 150.00, 60, 'Utilização de óleos essenciais para aromaterapia.'),
(3, 4, 180.00, 90, 'Inclui peeling de diamante para uma renovação celular mais intensa.'),
(4, 5, 90.00, 75, 'Uma experiência completa de relaxamento e cuidado para o homem moderno.'),
(5, 2, 75.00, 90, 'Esmaltes importados e opções hipoalergênicas disponíveis.');

INSERT INTO Disponibilidade (id_usuario_profissional, dia_semana, hora_inicio, hora_fim) VALUES
(1, 'Segunda', '09:00:00', '18:00:00'),
(1, 'Terca', '09:00:00', '18:00:00'),
(2, 'Quarta', '10:00:00', '20:00:00'),
(3, 'Sexta', '08:00:00', '17:00:00'),
(4, 'Sabado', '09:00:00', '15:00:00');

INSERT INTO Agendamento (id_cliente, id_profissional_servico, data_hora_inicio, data_hora_fim, status) VALUES
(6, 1, '2025-10-13 10:00:00', '2025-10-13 11:00:00', 'Confirmado'), -- Cliente Fernando (6) com a Profissional Ana (serviço 1)
(7, 2, '2025-10-15 14:30:00', '2025-10-15 15:30:00', 'Pendente'), -- Cliente Gabriela (7) com o Profissional Bruno (serviço 2)
(6, 3, '2025-10-17 11:00:00', '2025-10-17 12:30:00', 'Confirmado'), -- Cliente Fernando (6) com a Profissional Carla (serviço 3)
(7, 4, '2025-09-28 13:00:00', '2025-09-28 14:15:00', 'Concluido'), -- Cliente Gabriela (7) com o Profissional Daniel (serviço 4)
(6, 5, '2025-09-30 16:00:00', '2025-09-30 17:30:00', 'Cancelado'); -- Cliente Fernando (6) com a Profissional Eliana (serviço 5)