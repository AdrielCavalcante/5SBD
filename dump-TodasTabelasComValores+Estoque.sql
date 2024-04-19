-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 19/04/2024 às 23:07
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
('J1947', '2024-04-10', 'BR0027152025SP', 27152025, 'Tablet', 1, 1400.00, 12.75, 'Fanny@gmail.com', 'Fanny', 'Rua das flores 18', 'SP', 'Brasil', '01153-000'),
('A2023', '2024-04-15', 'BR0030102010AM', 30102010, 'Tablet', 1, 2000.00, 223.49, 'Rogerio@gmail.com', 'Rogério', 'Parintins lote XV', 'AM', 'Brasil', '69151-020'),
('A2023', '2024-04-16', 'BR0020052015AM', 20052015, 'Monitor', 3, 1500.50, 223.49, 'Rogerio@gmail.com', 'Rogério', 'Parintins lote XV', 'AM', 'Brasil', '69151-020');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `endereco` varchar(190) NOT NULL,
  `UF` char(2) NOT NULL,
  `Pais` varchar(30) NOT NULL,
  `CEP` char(9) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unico` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `nome`, `email`, `endereco`, `UF`, `Pais`, `CEP`) VALUES
(1, 'Kleberson', 'Kleberson@gmail.com', 'Rua Epamimondas 47', 'RJ', 'Brasil', '20406-506'),
(2, 'Fanny', 'Fanny@gmail.com', 'Rua das flores 18', 'SP', 'Brasil', '01153-000'),
(3, 'Rogério', 'Rogerio@gmail.com', 'Parintins lote XV', 'AM', 'Brasil', '69151-020');

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

DROP TABLE IF EXISTS `estoque`;
CREATE TABLE IF NOT EXISTS `estoque` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantidade` int NOT NULL,
  `idProduto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idProduto` (`idProduto`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `quantidade`, `idProduto`) VALUES
(1, 2, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 3, 7);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itempedido`
--

DROP TABLE IF EXISTS `itempedido`;
CREATE TABLE IF NOT EXISTS `itempedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantidade` int NOT NULL,
  `idPedido` varchar(50) NOT NULL,
  `idProduto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idPedido` (`idPedido`),
  KEY `idProduto` (`idProduto`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `itempedido`
--

INSERT INTO `itempedido` (`id`, `quantidade`, `idPedido`, `idProduto`) VALUES
(1, 2, 'G2075', 1),
(2, 1, 'G2075', 2),
(3, 1, 'G2075', 3),
(4, 1, 'J1947', 4),
(5, 1, 'J1947', 5),
(6, 1, 'A2023', 6),
(7, 3, 'A2023', 7);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

DROP TABLE IF EXISTS `pedido`;
CREATE TABLE IF NOT EXISTS `pedido` (
  `id` varchar(50) NOT NULL,
  `dataPedido` date NOT NULL,
  `frete` decimal(10,2) DEFAULT NULL,
  `valorTotal` decimal(10,2) NOT NULL,
  `idCliente` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idCliente` (`idCliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `pedido`
--

INSERT INTO `pedido` (`id`, `dataPedido`, `frete`, `valorTotal`, `idCliente`) VALUES
('G2075', '2024-03-19', 33.25, 8426.75, 1),
('J1947', '2024-04-10', 12.75, 2149.99, 2),
('A2023', '2024-04-15', 223.49, 6501.50, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

DROP TABLE IF EXISTS `produto`;
CREATE TABLE IF NOT EXISTS `produto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(190) NOT NULL,
  `SKU` varchar(16) NOT NULL,
  `UPC` varchar(13) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku_unico` (`SKU`),
  UNIQUE KEY `upc_unico` (`UPC`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id`, `nome`, `SKU`, `UPC`, `valor`) VALUES
(1, 'Notebook', 'BR0022152024RJ', '22152024', 2750.75),
(2, 'Tablet', 'BR0022152025RJ', '22152025', 1200.00),
(3, 'Celular', 'BR0022152026RJ', '22152026', 1725.25),
(4, 'Microfone', 'BR0023192024SP', '23192024', 749.99),
(5, 'Tablet', 'BR0027152025SP', '27152025', 1400.00),
(6, 'Tablet', 'BR0030102010AM', '30102010', 2000.00),
(7, 'Monitor', 'BR0020052015AM', '20052015', 1500.50);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
