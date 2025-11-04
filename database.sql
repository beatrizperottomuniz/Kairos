-- 🔄 RECRIAÇÃO DO BANCO DE DADOS
DROP DATABASE IF EXISTS Kairos;
CREATE DATABASE Kairos;
USE Kairos;

-- 👤 USUÁRIOS (CLIENTES E PROFISSIONAIS)
CREATE TABLE Usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo_conta ENUM('cliente', 'profissional') NOT NULL
);

-- 💼 PERFIL PROFISSIONAL
CREATE TABLE Perfil_Profissional (
    id_usuario INT PRIMARY KEY,
    especialidade VARCHAR(100),
    biografia VARCHAR(400),

    CONSTRAINT fk_perfil_usuario
        FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE
);

-- 💇‍♀️ SERVIÇOS
CREATE TABLE Servico (
    id_servico INT PRIMARY KEY AUTO_INCREMENT,
    nome_servico VARCHAR(100) NOT NULL,
    descricao_geral TEXT
);

-- 🔗 RELAÇÃO PROFISSIONAL–SERVIÇO
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

-- 🕐 DISPONIBILIDADE FIXA SEMANAL
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

-- 📅 AGENDAMENTOS
CREATE TABLE Agendamento (
    id_agendamento INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_profissional_servico INT NOT NULL,
    data_hora_inicio DATETIME NOT NULL,
    data_hora_fim DATETIME NOT NULL,
    status ENUM('Pendente', 'Confirmado', 'Cancelado', 'Concluido') NOT NULL DEFAULT 'Pendente',
    observacao VARCHAR(400),

    CONSTRAINT fk_agend_cliente
        FOREIGN KEY (id_cliente) REFERENCES Usuario(id_usuario),
    CONSTRAINT fk_agend_prof_servico
        FOREIGN KEY (id_profissional_servico) REFERENCES Profissional_Servico(id_profissional_servico)
);

-- ⭐ AVALIAÇÕES DE CLIENTES
CREATE TABLE Avaliacao (
    id_avaliacao INT PRIMARY KEY AUTO_INCREMENT,
    id_agendamento INT NOT NULL,
    id_cliente INT NOT NULL,
    id_profissional INT NOT NULL,
    nota INT CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT,
    data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_av_agendamento
        FOREIGN KEY (id_agendamento) REFERENCES Agendamento(id_agendamento)
        ON DELETE CASCADE,
    CONSTRAINT fk_av_cliente
        FOREIGN KEY (id_cliente) REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE,
    CONSTRAINT fk_av_profissional
        FOREIGN KEY (id_profissional) REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE
);

-- 🏠 LOCAL DE ATENDIMENTO
CREATE TABLE Local_Atendimento (
    id_local INT PRIMARY KEY AUTO_INCREMENT,
    id_profissional INT NOT NULL,
    endereco VARCHAR(255),
    CEP VARCHAR(8),
    tipo_local ENUM('presencial', 'domicilio', 'online') NOT NULL,
    observacoes TEXT,

    CONSTRAINT fk_local_prof
        FOREIGN KEY (id_profissional) REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE
);

-- 🚫 BLOQUEIOS DE DISPONIBILIDADE
CREATE TABLE Disponibilidade_Bloqueada (
    id_bloqueio INT PRIMARY KEY AUTO_INCREMENT,
    id_profissional INT NOT NULL,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NOT NULL,
    motivo VARCHAR(255),
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bloq_prof
        FOREIGN KEY (id_profissional) REFERENCES Usuario(id_usuario)
        ON DELETE CASCADE
);

-- 📥 INSERÇÃO DE DADOS DE EXEMPLO
-- Usuários
INSERT INTO Usuario (nome, email, senha, tipo_conta) VALUES
('Ana Silva', 'ana.silva@example.com', 'senha1', 'profissional'),
('Bruno Costa', 'bruno.costa@example.com', 'senha2', 'profissional'),
('Carla Dias', 'carla.dias@example.com', 'senha3', 'profissional'),
('Daniel Faria', 'daniel.faria@example.com', 'senha4', 'profissional'),
('Eliana Santos', 'eliana.santos@example.com', 'senha5', 'profissional'),
('Fernando Lima', 'fernando.lima@example.com', 'senha6', 'cliente'),
('Gabriela Mota', 'gabriela.mota@example.com', 'senha7', 'cliente');

-- Perfis Profissionais
INSERT INTO Perfil_Profissional (id_usuario, especialidade, biografia) VALUES
(1, 'Cabeleireira', 'Especialista em cortes modernos e coloração. Mais de 10 anos de experiência.'),
(2, 'Massoterapeuta', 'Focado em massagens terapêuticas e de relaxamento profundo.'),
(3, 'Esteticista', 'Especialista em tratamentos faciais, incluindo limpeza de pele e peelings.'),
(4, 'Barbeiro', 'Mestre em cortes clássicos e modernos, além de cuidados com a barba.'),
(5, 'Manicure e Pedicure', 'Técnicas avançadas em cuidados com unhas, incluindo unhas de gel e spa dos pés.');

-- Serviços
INSERT INTO Servico (nome_servico, descricao_geral) VALUES
('Corte de Cabelo Feminino', 'Corte, lavagem e finalização para todos os tipos de cabelo.'),
('Manicure e Pedicure Completa', 'Cutilagem, esmaltação e hidratação para mãos e pés.'),
('Massagem Relaxante', 'Sessão de 60 minutos para alívio de tensões musculares e estresse.'),
('Limpeza de Pele Profunda', 'Extração de cravos, aplicação de máscaras e hidratação facial.'),
('Corte e Barba Terapia', 'Corte de cabelo masculino e tratamento completo para a barba com toalhas quentes.');

-- Profissional_Servico
INSERT INTO Profissional_Servico (id_usuario_profissional, id_servico, preco, duracao_minutos, descricao_adicional) VALUES
(1, 1, 120.00, 60, 'Finalização com escova inclusa.'),
(2, 3, 150.00, 60, 'Utilização de óleos essenciais para aromaterapia.'),
(3, 4, 180.00, 90, 'Inclui peeling de diamante para uma renovação celular mais intensa.'),
(4, 5, 90.00, 75, 'Uma experiência completa de relaxamento e cuidado para o homem moderno.'),
(5, 2, 75.00, 90, 'Esmaltes importados e opções hipoalergênicas disponíveis.');

-- Disponibilidade
INSERT INTO Disponibilidade (id_usuario_profissional, dia_semana, hora_inicio, hora_fim) VALUES
(1, 'Segunda', '09:00:00', '18:00:00'),
(1, 'Terca', '09:00:00', '18:00:00'),
(2, 'Quarta', '10:00:00', '20:00:00'),
(3, 'Sexta', '08:00:00', '17:00:00'),
(4, 'Sabado', '09:00:00', '15:00:00');

-- Agendamentos
INSERT INTO Agendamento (id_cliente, id_profissional_servico, data_hora_inicio, data_hora_fim, status, observacao) VALUES
(6, 1, '2025-10-13 10:00:00', '2025-10-13 11:00:00', 'Confirmado', 'Corte com finalização.'),
(7, 2, '2025-10-15 14:30:00', '2025-10-15 15:30:00', 'Pendente', 'Massagem com aromaterapia.'),
(6, 3, '2025-10-17 11:00:00', '2025-10-17 12:30:00', 'Confirmado', 'Limpeza de pele profunda.'),
(7, 4, '2025-09-28 13:00:00', '2025-09-28 14:15:00', 'Concluido', 'Corte e barba completa.'),
(6, 5, '2025-09-30 16:00:00', '2025-09-30 17:30:00', 'Cancelado', 'Manicure e pedicure completa.');

-- Avaliações
INSERT INTO Avaliacao (id_agendamento, id_cliente, id_profissional, nota, comentario) VALUES
(1, 6, 1, 5, 'Excelente atendimento, corte perfeito!'),
(4, 7, 4, 4, 'Muito bom, mas poderia ter sido um pouco mais rápido.');

-- Locais de Atendimento
INSERT INTO Local_Atendimento (id_profissional, endereco, CEP, tipo_local, observacoes) VALUES
(1, 'Rua das Flores, 123, Sala 101', '80010000', 'presencial', 'Estacionamento gratuito.'),
(2, 'Avenida Central, 456, Clínica Bem-Estar', '80020000', 'presencial', 'Ambiente climatizado e relaxante.'),
(3, 'Rua da Paz, 789, Espaço Beleza', '80030000', 'presencial', 'Atendimento apenas com hora marcada.'),
(4, 'Travessa do Comércio, 10, Barbearia Nobre', '80040000', 'presencial', 'Serviços premium com café incluso.'),
(5, NULL, NULL, 'domicilio', 'Atendimento a domicílio mediante agendamento.');

-- Bloqueios de Disponibilidade
INSERT INTO Disponibilidade_Bloqueada (id_profissional, data_inicio, data_fim, motivo) VALUES
(1, '2025-12-24 00:00:00', '2025-12-26 23:59:59', 'Recesso de Natal'),
(2, '2025-11-10 09:00:00', '2025-11-10 18:00:00', 'Consulta médica pessoal');
