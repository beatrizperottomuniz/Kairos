-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: Kairos
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Agendamento`
--

DROP TABLE IF EXISTS `Agendamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Agendamento` (
  `id_agendamento` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) DEFAULT NULL,
  `id_profissional_servico` int(11) DEFAULT NULL,
  `nome_servico` varchar(100) DEFAULT NULL,
  `data_hora_inicio` datetime NOT NULL,
  `data_hora_fim` datetime NOT NULL,
  `status` enum('Pendente','Confirmado','Cancelado','Concluido') NOT NULL DEFAULT 'Pendente',
  `observacao` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id_agendamento`),
  KEY `fk_agend_cliente` (`id_cliente`),
  KEY `fk_agend_prof_servico` (`id_profissional_servico`),
  CONSTRAINT `fk_agend_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `Usuario` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `fk_agend_prof_servico` FOREIGN KEY (`id_profissional_servico`) REFERENCES `Profissional_Servico` (`id_profissional_servico`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Agendamento`
--

LOCK TABLES `Agendamento` WRITE;
/*!40000 ALTER TABLE `Agendamento` DISABLE KEYS */;
INSERT INTO `Agendamento` VALUES (1,6,1,'Corte de Cabelo Feminino','2025-12-15 10:00:00','2025-10-13 11:00:00','Confirmado','Corte com finalização.'),(2,7,2,'Massagem Relaxante','2025-10-15 14:30:00','2025-10-15 15:30:00','Pendente','Massagem com aromaterapia.'),(3,6,3,'Limpeza de Pele','2025-11-08 11:00:00','2025-10-17 12:30:00','Confirmado','Limpeza de pele profunda.'),(4,7,4,'Corte e Barba','2025-09-28 13:00:00','2025-09-28 14:15:00','Concluido','Corte e barba completa.'),(5,6,5,'Manicure e Pedicure','2025-09-30 16:00:00','2025-09-30 17:30:00','Cancelado','Manicure e pedicure completa.'),(6,6,1,'Corte de Cabelo Feminino','2023-12-15 10:00:00','2025-10-13 11:00:00','Confirmado','Corte com finalização.'),(7,7,1,'Corte de Cabelo Feminino','2025-11-17 10:00:00','2025-11-17 11:00:00','Pendente','oo');
/*!40000 ALTER TABLE `Agendamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Avaliacao`
--

DROP TABLE IF EXISTS `Avaliacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Avaliacao` (
  `id_avaliacao` int(11) NOT NULL AUTO_INCREMENT,
  `id_agendamento` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_profissional` int(11) DEFAULT NULL,
  `nota` int(11) DEFAULT NULL CHECK (`nota` between 1 and 5),
  `data_avaliacao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_avaliacao`),
  KEY `fk_av_agendamento` (`id_agendamento`),
  KEY `fk_av_cliente` (`id_cliente`),
  KEY `fk_av_profissional` (`id_profissional`),
  CONSTRAINT `fk_av_agendamento` FOREIGN KEY (`id_agendamento`) REFERENCES `Agendamento` (`id_agendamento`) ON DELETE SET NULL,
  CONSTRAINT `fk_av_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `Usuario` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `fk_av_profissional` FOREIGN KEY (`id_profissional`) REFERENCES `Usuario` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Avaliacao`
--

LOCK TABLES `Avaliacao` WRITE;
/*!40000 ALTER TABLE `Avaliacao` DISABLE KEYS */;
INSERT INTO `Avaliacao` VALUES (1,1,6,1,5,'2025-11-08 23:02:49'),(3,4,7,4,3,'2025-11-09 12:42:34');
/*!40000 ALTER TABLE `Avaliacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Disponibilidade`
--

DROP TABLE IF EXISTS `Disponibilidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Disponibilidade` (
  `id_disponibilidade` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario_profissional` int(11) NOT NULL,
  `dia_semana` enum('Domingo','Segunda','Terca','Quarta','Quinta','Sexta','Sabado') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  PRIMARY KEY (`id_disponibilidade`),
  KEY `fk_disp_profissional` (`id_usuario_profissional`),
  CONSTRAINT `fk_disp_profissional` FOREIGN KEY (`id_usuario_profissional`) REFERENCES `Usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Disponibilidade`
--

LOCK TABLES `Disponibilidade` WRITE;
/*!40000 ALTER TABLE `Disponibilidade` DISABLE KEYS */;
INSERT INTO `Disponibilidade` VALUES (1,1,'Segunda','09:00:00','18:00:00'),(2,1,'Terca','09:00:00','18:00:00'),(3,2,'Quarta','10:00:00','20:00:00'),(4,3,'Sexta','08:00:00','17:00:00'),(5,4,'Sabado','09:00:00','15:00:00'),(6,5,'Sabado','09:00:00','15:00:00');
/*!40000 ALTER TABLE `Disponibilidade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Disponibilidade_Bloqueada`
--

DROP TABLE IF EXISTS `Disponibilidade_Bloqueada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Disponibilidade_Bloqueada` (
  `id_bloqueio` int(11) NOT NULL AUTO_INCREMENT,
  `id_profissional` int(11) NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime NOT NULL,
  PRIMARY KEY (`id_bloqueio`),
  KEY `fk_bloq_prof` (`id_profissional`),
  CONSTRAINT `fk_bloq_prof` FOREIGN KEY (`id_profissional`) REFERENCES `Usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Disponibilidade_Bloqueada`
--

LOCK TABLES `Disponibilidade_Bloqueada` WRITE;
/*!40000 ALTER TABLE `Disponibilidade_Bloqueada` DISABLE KEYS */;
INSERT INTO `Disponibilidade_Bloqueada` VALUES (1,1,'2025-12-24 00:00:00','2025-12-26 23:59:59'),(2,2,'2025-11-10 09:00:00','2025-11-10 18:00:00'),(3,5,'2025-11-10 09:00:00','2025-11-10 18:00:00');
/*!40000 ALTER TABLE `Disponibilidade_Bloqueada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Local_Atendimento`
--

DROP TABLE IF EXISTS `Local_Atendimento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Local_Atendimento` (
  `id_local` int(11) NOT NULL AUTO_INCREMENT,
  `id_profissional` int(11) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `CEP` varchar(8) DEFAULT NULL,
  `tipo_local` enum('presencial','domicilio','online') NOT NULL,
  `observacoes` text DEFAULT NULL,
  PRIMARY KEY (`id_local`),
  KEY `fk_local_prof` (`id_profissional`),
  CONSTRAINT `fk_local_prof` FOREIGN KEY (`id_profissional`) REFERENCES `Usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Local_Atendimento`
--

LOCK TABLES `Local_Atendimento` WRITE;
/*!40000 ALTER TABLE `Local_Atendimento` DISABLE KEYS */;
INSERT INTO `Local_Atendimento` VALUES (1,1,'Rua das Flores, 123, Sala 101','80010000','presencial','Estacionamento gratuito.'),(2,2,'Avenida Central, 456, Clínica Bem-Estar','80020000','presencial','Ambiente climatizado e relaxante.'),(3,3,'Rua da Paz, 789, Espaço Beleza','80030000','presencial','Atendimento apenas com hora marcada.'),(4,4,'Travessa do Comércio, 10, Barbearia Nobre','80040000','presencial','Serviços premium com café incluso.'),(5,5,NULL,NULL,'domicilio','Atendimento a domicílio mediante agendamento.');
/*!40000 ALTER TABLE `Local_Atendimento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Perfil_Profissional`
--

DROP TABLE IF EXISTS `Perfil_Profissional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Perfil_Profissional` (
  `id_usuario` int(11) NOT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `biografia` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  CONSTRAINT `fk_perfil_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Perfil_Profissional`
--

LOCK TABLES `Perfil_Profissional` WRITE;
/*!40000 ALTER TABLE `Perfil_Profissional` DISABLE KEYS */;
INSERT INTO `Perfil_Profissional` VALUES (1,'Cabeleireira','Especialista em cortes modernos e coloração. Mais de 10 anos de experiência.'),(2,'Massoterapeuta','Focado em massagens terapêuticas e de relaxamento profundo.'),(3,'Esteticista','Especialista em tratamentos faciais, incluindo limpeza de pele e peelings.'),(4,'Barbeiro','Mestre em cortes clássicos e modernos, além de cuidados com a barba.'),(5,'Manicure e Pedicure','Técnicas avançadas em cuidados com unhas, incluindo unhas de gel e spa dos pés.');
/*!40000 ALTER TABLE `Perfil_Profissional` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Profissional_Servico`
--

DROP TABLE IF EXISTS `Profissional_Servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Profissional_Servico` (
  `id_profissional_servico` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario_profissional` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `duracao_minutos` int(10) unsigned NOT NULL,
  `descricao_adicional` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id_profissional_servico`),
  UNIQUE KEY `id_usuario_profissional` (`id_usuario_profissional`,`id_servico`,`descricao_adicional`),
  KEY `fk_ps_servico` (`id_servico`),
  CONSTRAINT `fk_ps_profissional` FOREIGN KEY (`id_usuario_profissional`) REFERENCES `Usuario` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `fk_ps_servico` FOREIGN KEY (`id_servico`) REFERENCES `Servico` (`id_servico`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Profissional_Servico`
--

LOCK TABLES `Profissional_Servico` WRITE;
/*!40000 ALTER TABLE `Profissional_Servico` DISABLE KEYS */;
INSERT INTO `Profissional_Servico` VALUES (1,1,1,120.00,60,'Finalização com escova inclusa.'),(2,2,3,150.00,60,'Utilização de óleos essenciais para aromaterapia.'),(3,3,4,180.00,90,'Inclui peeling de diamante para uma renovação celular mais intensa.'),(4,4,5,90.00,75,'Uma experiência completa de relaxamento e cuidado para o homem moderno.'),(5,5,2,75.00,90,'Esmaltes importados e opções hipoalergênicas disponíveis.');
/*!40000 ALTER TABLE `Profissional_Servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Servico`
--

DROP TABLE IF EXISTS `Servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Servico` (
  `id_servico` int(11) NOT NULL AUTO_INCREMENT,
  `nome_servico` varchar(100) NOT NULL,
  `descricao_geral` text DEFAULT NULL,
  PRIMARY KEY (`id_servico`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Servico`
--

LOCK TABLES `Servico` WRITE;
/*!40000 ALTER TABLE `Servico` DISABLE KEYS */;
INSERT INTO `Servico` VALUES (1,'Corte de Cabelo Feminino','Corte, lavagem e finalização para todos os tipos de cabelo.'),(2,'Manicure e Pedicure Completa','Cutilagem, esmaltação e hidratação para mãos e pés.'),(3,'Massagem Relaxante','Sessão de 60 minutos para alívio de tensões musculares e estresse.'),(4,'Limpeza de Pele Profunda','Extração de cravos, aplicação de máscaras e hidratação facial.'),(5,'Corte e Barba Terapia','Corte de cabelo masculino e tratamento completo para a barba com toalhas quentes.');
/*!40000 ALTER TABLE `Servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Usuario`
--

DROP TABLE IF EXISTS `Usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_conta` enum('cliente','profissional') NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Usuario`
--

LOCK TABLES `Usuario` WRITE;
/*!40000 ALTER TABLE `Usuario` DISABLE KEYS */;
INSERT INTO `Usuario` VALUES (1,'Ana Silva','ana.silva@example.com','senha1','profissional'),(2,'Bruno Costa','bruno.costa@example.com','senha2','profissional'),(3,'Carla Dias','carla.dias@example.com','senha3','profissional'),(4,'Daniel Faria','daniel.faria@example.com','senha4','profissional'),(5,'Eliana Santos','eliana.santos@example.com','senha5','profissional'),(6,'Fernando Lima','fernando.lima@example.com','senha6','cliente'),(7,'Gabriela Mota','gabriela.mota@example.com','senha7','cliente');
/*!40000 ALTER TABLE `Usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-09 15:49:13
