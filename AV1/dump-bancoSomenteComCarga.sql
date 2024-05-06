-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 05/05/2024 às 23:22
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
-- Banco de dados: `bazartemtudo`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carga`
--

DROP TABLE IF EXISTS `carga`;
CREATE TABLE IF NOT EXISTS `carga` (
  `order-id` varchar(7) DEFAULT NULL,
  `order-item-id` varchar(7) DEFAULT NULL,
  `purchase-date` varchar(10) DEFAULT NULL,
  `payments-date` varchar(10) DEFAULT NULL,
  `buyer-email` varchar(23) DEFAULT NULL,
  `buyer-name` varchar(18) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `buyer-phone-number` varchar(16) DEFAULT NULL,
  `sku` varchar(6) DEFAULT NULL,
  `upc` bigint DEFAULT NULL,
  `product-name` varchar(9) DEFAULT NULL,
  `quantity-purchased` int DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `item-price` decimal(5,2) DEFAULT NULL,
  `ship-service-level` varchar(8) DEFAULT NULL,
  `ship-address-1` varchar(22) DEFAULT NULL,
  `ship-address-2` varchar(10) DEFAULT NULL,
  `ship-address-3` varchar(13) DEFAULT NULL,
  `ship-city` varchar(14) DEFAULT NULL,
  `ship-state` varchar(2) DEFAULT NULL,
  `ship-postal-code` varchar(5) DEFAULT NULL,
  `ship-country` varchar(6) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `carga`
--

INSERT INTO `carga` (`order-id`, `order-item-id`, `purchase-date`, `payments-date`, `buyer-email`, `buyer-name`, `cpf`, `buyer-phone-number`, `sku`, `upc`, `product-name`, `quantity-purchased`, `currency`, `item-price`, `ship-service-level`, `ship-address-1`, `ship-address-2`, `ship-address-3`, `ship-city`, `ship-state`, `ship-postal-code`, `ship-country`) VALUES
('XZ1Y2W3', 'ITEM001', '2024-04-30', '2024-05-01', 'epamimondas@example.com', 'Epamimondas Cícero', '123.456.789-00', '55 11 98765-4321', 'SKU123', 12345678912, 'Produto A', 2, 'BRL', 80.00, 'Express', 'Rua das Palmeiras', '', 'Casa 1', 'São Paulo', 'SP', '01234', 'Brasil'),
('XZ1Y2W3', 'ITEM002', '2024-04-30', '2024-05-01', 'epamimondas@example.com', 'Epamimondas Cícero', '123.456.789-00', '55 11 98765-4321', 'SKU456', 123456789123, 'Produto B', 1, 'BRL', 120.00, 'Standard', 'Rua dos Ipês', '', 'Apartamento 2', 'Rio de Janeiro', 'RJ', '45678', 'Brasil'),
('XZ1Y2W3', 'ITEM003', '2024-04-29', '2024-04-30', 'epamimondas@example.com', 'Epamimondas Cícero', '123.456.789-00', '55 11 98765-4321', 'SKU789', 987654321987, 'Produto C', 3, 'BRL', 50.00, 'Standard', 'Rua das Flores', '', 'Casa 3', 'Belo Horizonte', 'MG', '98765', 'Brasil'),
('UV9T8S7', 'ITEM004', '2024-04-30', '2024-05-01', 'zenóbio@example.com', 'Zenóbio Prudêncio', '987.654.321-00', '55 21 98765-4321', 'SKU321', 789654123987, 'Produto D', 1, 'BRL', 25.00, 'Express', 'Rua das Amendoeiras', '', 'Casa 4', 'Curitiba', 'PR', '65432', 'Brasil'),
('UV9T8S7', 'ITEM005', '2024-04-29', '2024-04-30', 'zenóbio@example.com', 'Zenóbio Prudêncio', '987.654.321-00', '55 21 98765-4321', 'SKU654', 456789123456, 'Produto E', 5, 'BRL', 30.00, 'Standard', 'Rua das Mangueiras', '', 'Casa 5', 'Porto Alegre', 'RS', '12345', 'Brasil'),
('AB6C5D4', 'ITEM006', '2024-04-30', '2024-05-01', 'adelmar@example.com', 'Adelmar Pantaleão', '654.321.987-00', '55 31 98765-4321', 'SKU987', 789123456789, 'Produto F', 2, 'BRL', 35.00, 'Express', 'Rua das Palmeiras', '', 'Apartamento 6', 'Brasília', 'DF', '98765', 'Brasil'),
('AB6C5D4', 'ITEM007', '2024-04-30', '2024-05-01', 'adelmar@example.com', 'Adelmar Pantaleão', '654.321.987-00', '55 31 98765-4321', 'SKU890', 321654987321, 'Produto G', 4, 'BRL', 40.00, 'Standard', 'Rua das Oliveiras', '', 'Casa 7', 'Salvador', 'BA', '54321', 'Brasil'),
('ZYXWVUT', 'ITEM008', '2024-04-29', '2024-04-30', 'pelópidas@example.com', 'Pelópidas Frota', '321.654.987-00', '55 41 98765-4321', 'SKU678', 987321654987, 'Produto H', 2, 'BRL', 45.00, 'Express', 'Rua das Jabuticabeiras', '', 'Casa 8', 'Recife', 'PE', '67890', 'Brasil'),
('ZYXWVUT', 'ITEM009', '2024-04-30', '2024-05-01', 'pelópidas@example.com', 'Pelópidas Frota', '321.654.987-00', '55 41 98765-4321', 'SKU012', 147258369147, 'Produto I', 1, 'BRL', 55.00, 'Standard', 'Rua das Acácias', '', 'Casa 9', 'Belo Horizonte', 'MG', '78901', 'Brasil'),
('QW3E4R5', 'ITEM010', '2024-04-29', '2024-04-30', 'zeferino@example.com', 'Zeferino Rufino', '951.753.468-00', '55 51 98765-4321', 'SKU345', 963852741963, 'Produto J', 3, 'BRL', 60.00, 'Express', 'Rua das Laranjeiras', '', 'Casa 10', 'Goiânia', 'GO', '23456', 'Brasil');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
