-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 16/04/2024 às 22:05
-- Versão do servidor: 8.2.0
-- Versão do PHP: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sbd`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carga`
--

DROP TABLE IF EXISTS `carga`;
CREATE TABLE IF NOT EXISTS `carga` (
  `CodigoPedido` varchar(5) DEFAULT NULL,
  `DataPedido` varchar(10) DEFAULT NULL,
  `SKU` varchar(14) DEFAULT NULL,
  `UPC` int DEFAULT NULL,
  `NomeProduto` varchar(9) DEFAULT NULL,
  `QTD` int DEFAULT NULL,
  `Valor` decimal(6,2) DEFAULT NULL,
  `Frete` decimal(5,2) DEFAULT NULL,
  `Email` varchar(19) DEFAULT NULL,
  `NomeComprador` varchar(9) DEFAULT NULL,
  `Endereco` varchar(18) DEFAULT NULL,
  `UF` varchar(2) DEFAULT NULL,
  `Pais` varchar(6) DEFAULT NULL,
  `CEP` varchar(9) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `carga`
--

INSERT INTO `carga` (`CodigoPedido`, `DataPedido`, `SKU`, `UPC`, `NomeProduto`, `QTD`, `Valor`, `Frete`, `Email`, `NomeComprador`, `Endereco`, `UF`, `Pais`, `CEP`) VALUES
('G2075', '2024-03-19', 'BR0022152024RJ', 22152024, 'Notebook', 2, 2750.75, 33.25, 'Kleberson@gmail.com', 'Kleberson', 'Rua Epamimondas 47', 'RJ', 'Brasil', '20406-506'),
('G2075', '2024-03-19', 'BR0022152025RJ', 22152025, 'Tablet', 1, 1200.00, 33.25, 'Kleberson@gmail.com', 'Kleberson', 'Rua Epamimondas 47', 'RJ', 'Brasil', '20406-506'),
('G2075', '2024-03-19', 'BR0022152026RJ', 22152026, 'Celular', 1, 1725.25, 33.25, 'Kleberson@gmail.com', 'Kleberson', 'Rua Epamimondas 47', 'RJ', 'Brasil', '20406-506'),
('J1947', '2024-04-10', 'BR0023192024SP', 23192024, 'Microfone', 1, 749.99, 12.75, 'Fanny@gmail.com', 'Fanny', 'Rua das flores 18', 'SP', 'Brasil', '01153-000'),
('J1947', '2024-04-10', 'BR0022152025SP', 22152025, 'Tablet', 1, 1400.00, 12.75, 'Fanny@gmail.com', 'Fanny', 'Rua das flores 18', 'SP', 'Brasil', '01153-000'),
('A2023', '2024-04-15', 'BR0030102010AM', 30102010, 'Tablet', 1, 2000.00, 223.49, 'Rogerio@gmail.com', 'Rogério', 'Parintins lote XV', 'AM', 'Brasil', '69151-020'),
('A2023', '2024-04-16', 'BR0020052015AM', 20052015, 'Monitor', 3, 1500.50, 223.49, 'Rogerio@gmail.com', 'Rogério', 'Parintins lote XV', 'AM', 'Brasil', '69151-020');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
