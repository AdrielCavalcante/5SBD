-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 07/05/2024 às 02:42
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

DELIMITER $$
--
-- Procedimentos
--
DROP PROCEDURE IF EXISTS `AtualizarEstoqueEInserirMovimentacao`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AtualizarEstoqueEInserirMovimentacao` (IN `pedido_id` VARCHAR(50))   BEGIN
    -- Variáveis
    DECLARE produto_id INT;
    DECLARE quantidade_pedido INT;
    DECLARE done INT DEFAULT FALSE;
    DECLARE data_atual DATETIME;
    
    -- Cursor para percorrer os itens do pedido
    DECLARE cur_itenspedido CURSOR FOR
        SELECT id_produto, quantidade
        FROM itempedido
        WHERE id_pedido = pedido_id;
    
    -- Declaração de handler para cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Inicialização
    SET data_atual = NOW();
    SET done = FALSE;
    
    -- Loop para percorrer os itens do pedido
    OPEN cur_itenspedido;
    itenspedido_loop:LOOP
        FETCH cur_itenspedido INTO produto_id, quantidade_pedido;
        IF done THEN
            LEAVE itenspedido_loop;
        END IF;
        
        -- Atualiza a quantidade no estoque
        UPDATE estoques
        SET quantidade = quantidade - quantidade_pedido
        WHERE id_produto = produto_id;
        
        -- Insere na tabela de movimentações
        INSERT INTO movimentacoes (quantidade, dataMovimentacao, id_pedido, id_produto)
        VALUES (quantidade_pedido, data_atual, pedido_id, produto_id);
    END LOOP itenspedido_loop;
    
    CLOSE cur_itenspedido;
END$$

DROP PROCEDURE IF EXISTS `AtualizarValorTotalPedido`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AtualizarValorTotalPedido` ()   BEGIN

    UPDATE pedidos pe SET valorTotal = (
        SELECT SUM(ip.valor * ip.quantidade) 
        FROM itempedido ip
        WHERE ip.id_pedido = pe.id
    );

END$$

DROP PROCEDURE IF EXISTS `CriarOperacoes`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CriarOperacoes` ()   BEGIN

    TRUNCATE clientes;
    TRUNCATE enderecos;
    TRUNCATE pedidos;
    TRUNCATE itempedido;
    TRUNCATE produtos;
    TRUNCATE estoques;
    TRUNCATE movimentacoes;
    TRUNCATE compras;

    CALL InserindoDadosClientesComEndereco();

    CALL InserindoDadosProdutosComEstoque();
    
    UPDATE estoques SET quantidade = 4 WHERE id = 5;

    CALL InserindoDadosPedidos();

    CALL InserindoDadosItensPedidos();

    CALL ProcessarPedidos();
END$$

DROP PROCEDURE IF EXISTS `ExisteEstoqueSuficienteParaPedido`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ExisteEstoqueSuficienteParaPedido` (IN `pedido_id` VARCHAR(50), OUT `estoque_suficiente` BOOLEAN)   BEGIN
    DECLARE quantidade_produto INT;
    DECLARE quantidade_estoque INT;
    DECLARE produto_id INT;
    DECLARE estoque_disponivel INT DEFAULT 0;

    -- Cursor para obter os itens do pedido
    DECLARE cur_itenspedido CURSOR FOR
        SELECT id_produto, quantidade
        FROM itempedido
        WHERE id_pedido = pedido_id;
    
    -- Declaração de handler para cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET estoque_suficiente = FALSE;

    -- Inicialização
    SET estoque_suficiente = TRUE;

    -- Loop para percorrer os itens do pedido
    OPEN cur_itenspedido;
    itenspedido_loop:LOOP
        FETCH cur_itenspedido INTO produto_id, quantidade_produto;
        IF NOT estoque_suficiente THEN
            LEAVE itenspedido_loop;
        END IF;

        -- Verifica a quantidade disponível em estoque para o produto
        SELECT quantidade INTO quantidade_estoque
        FROM estoques
        WHERE id_produto = produto_id;

        -- Verifica se a quantidade disponível em estoque é suficiente para o pedido
        IF quantidade_estoque >= quantidade_produto THEN
            SET estoque_disponivel = estoque_disponivel + 1;
        END IF;
    END LOOP itenspedido_loop;

    CLOSE cur_itenspedido;

    -- Se todos os produtos tiverem estoque suficiente, define estoque_suficiente como TRUE
    IF estoque_disponivel = (SELECT COUNT(*) FROM itempedido WHERE id_pedido = pedido_id) THEN
        SET estoque_suficiente = TRUE;
    ELSE
        SET estoque_suficiente = FALSE;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `InserindoDadosClientesComEndereco`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `InserindoDadosClientesComEndereco` ()   BEGIN

    -- Inserindo dados dos clientes e recuperando o ID do cliente recém-inserido
    INSERT INTO clientes (nome, email, telefone, cpf)
    SELECT DISTINCT
        ca.`buyer-name`, 
        ca.`buyer-email`, 
        ca.`buyer-phone-number`, 
        ca.cpf
    FROM carga ca
    LEFT JOIN clientes cl ON (cl.`email` = ca.`buyer-email`)
    WHERE cl.email IS NULL;

    -- Inserindo dados de endereço baseados nos clientes inseridos
    INSERT INTO enderecos (nivel_servico_envio, endereco_envio_linha1, endereco_envio_linha2, endereco_envio_linha3, cidade_envio, estado_envio, codigo_postal_envio, pais_envio, id_cliente)
    SELECT
        ca.`ship-service-level`,
        ca.`ship-address-1`,
        ca.`ship-address-2`,
        ca.`ship-address-3`,
        ca.`ship-city`,
        ca.`ship-state`,
        ca.`ship-country`,
        ca.`ship-postal-code`,
        (SELECT id FROM clientes WHERE clientes.`email` = ca.`buyer-email`)
    FROM carga ca;
END$$

DROP PROCEDURE IF EXISTS `InserindoDadosItensPedidos`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `InserindoDadosItensPedidos` ()   BEGIN

    INSERT INTO itempedido (id, valor, quantidade, id_pedido, id_produto)
    SELECT
        ca.`order-item-id`,
        ca.`item-price`,
        ca.`quantity-purchased`,
        (SELECT id FROM pedidos WHERE pedidos.id = ca.`order-id`),
        (SELECT id FROM produtos WHERE produtos.SKU = ca.`sku`)
    FROM carga ca;

    CALL AtualizarValorTotalPedido();
END$$

DROP PROCEDURE IF EXISTS `InserindoDadosPedidos`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `InserindoDadosPedidos` ()   BEGIN

    INSERT INTO pedidos (id, dataPedido, dataPagamento, moeda, valorTotal, id_cliente)
    SELECT
        ca.`order-id`,
        ca.`purchase-date`,
        ca.`payments-date`,
        ca.`currency`,
        0,
        (SELECT id FROM clientes cl WHERE cl.`email` = ca.`buyer-email`)
    FROM carga ca
    INNER JOIN clientes cl ON (cl.`email` = ca.`buyer-email`)
    WHERE NOT EXISTS (
        SELECT 1
        FROM pedidos p
        WHERE p.`id` = ca.`order-id`
    )
    GROUP BY ca.`order-id`;
END$$

DROP PROCEDURE IF EXISTS `InserindoDadosProdutosComEstoque`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `InserindoDadosProdutosComEstoque` ()   BEGIN

    INSERT INTO produtos (nome, SKU, UPC)
    SELECT
        ca.`product-name`,
        ca.`sku`,
        ca.`upc`
    FROM carga ca
    LEFT JOIN produtos p ON (ca.`sku` = p.`sku` OR ca.`upc` = p.`UPC`);

    INSERT INTO estoques (quantidade, id_produto)
    SELECT 
        ca.`quantity-purchased`,
        (SELECT id FROM produtos po WHERE po.`SKU` = ca.`sku`)
    FROM carga ca;

END$$

DROP PROCEDURE IF EXISTS `ProcessarPedidos`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ProcessarPedidos` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE id_pedido_atual VARCHAR(50);
    DECLARE estoque_suficiente BOOLEAN;
    
    -- Cursor para percorrer os pedidos em ordem decrescente de valor total
    DECLARE cur_pedidos CURSOR FOR
        SELECT id
        FROM pedidos
        ORDER BY valorTotal DESC;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Inicialização
    SET done = FALSE;
    
    -- Loop para percorrer os pedidos em ordem decrescente de valor total
    OPEN cur_pedidos;
    pedido_loop: LOOP
        FETCH cur_pedidos INTO id_pedido_atual;
        IF done THEN
            LEAVE pedido_loop;
        END IF;
        
        -- Verifica se há estoque suficiente para o pedido atual
        CALL ExisteEstoqueSuficienteParaPedido(id_pedido_atual, @estoque_suficiente);
        
        -- Atualiza o estoque e registra a movimentação se houver estoque suficiente
        IF @estoque_suficiente THEN
            CALL AtualizarEstoqueEInserirMovimentacao(id_pedido_atual);
            -- Atualiza o status do pedido para "Concluído"
            UPDATE pedidos SET status = 'Concluído' WHERE id = id_pedido_atual;
        ELSE
            -- Atualiza o status do pedido para "Faltando Compra"
            UPDATE pedidos SET status = 'Faltando produto' WHERE id = id_pedido_atual;
            -- Realiza a compra se algum produto estiver em falta
            CALL RealizarCompra(id_pedido_atual);
            -- Atualiza o status do pedido para "Compra Realizada"
            UPDATE pedidos SET status = 'Compra Realizada' WHERE id = id_pedido_atual;
        END IF;
    END LOOP pedido_loop;
    
    CLOSE cur_pedidos;
END$$

DROP PROCEDURE IF EXISTS `RealizarCompra`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `RealizarCompra` (IN `pedido_id` VARCHAR(50))   BEGIN
    DECLARE produto_id INT;
    DECLARE quantidade_pedido INT;
    DECLARE quantidade_estoque INT;
    DECLARE quantidade_em_falta INT;
    DECLARE done BOOLEAN DEFAULT FALSE;

    -- Cursor para percorrer os itens do pedido
    DECLARE cur_itenspedido CURSOR FOR
        SELECT id_produto, quantidade
        FROM itempedido
        WHERE id_pedido = pedido_id;

    -- Verifica se o pedido está com status "Faltando produto"
    IF (SELECT status FROM pedidos WHERE id = pedido_id) = 'Faltando produto' THEN

        -- Loop para percorrer os itens do pedido
        OPEN cur_itenspedido;
        itenspedido_loop:LOOP
            FETCH cur_itenspedido INTO produto_id, quantidade_pedido;
            IF done THEN
                LEAVE itenspedido_loop;
            END IF;

            -- Verifica a quantidade em estoque para o produto
            SELECT quantidade INTO quantidade_estoque
            FROM estoques
            WHERE id_produto = produto_id;

            -- Calcula a quantidade em falta
            SET quantidade_em_falta = GREATEST(quantidade_pedido - COALESCE(quantidade_estoque, 0), 0);

            -- Insere na tabela de compras, independentemente do estoque
            INSERT INTO compras (id_produto, quantidade, data_compra)
            VALUES (produto_id, quantidade_em_falta, NOW());

        END LOOP itenspedido_loop;

        CLOSE cur_itenspedido;
    END IF;
END$$

DELIMITER ;

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `telefone` varchar(190) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unico` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `cpf`) VALUES
(1, 'Epamimondas Cícero', 'epamimondas@example.com', '55 11 98765-4321', '123.456.789-00'),
(2, 'Zenóbio Prudêncio', 'zenóbio@example.com', '55 21 98765-4321', '987.654.321-00'),
(3, 'Adelmar Pantaleão', 'adelmar@example.com', '55 31 98765-4321', '654.321.987-00'),
(4, 'Pelópidas Frota', 'pelópidas@example.com', '55 41 98765-4321', '321.654.987-00'),
(5, 'Zeferino Rufino', 'zeferino@example.com', '55 51 98765-4321', '951.753.468-00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `compras`
--

DROP TABLE IF EXISTS `compras`;
CREATE TABLE IF NOT EXISTS `compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantidade` int NOT NULL,
  `data_compra` datetime NOT NULL,
  `id_produto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_produto` (`id_produto`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `compras`
--

INSERT INTO `compras` (`id`, `quantidade`, `data_compra`, `id_produto`) VALUES
(1, 0, '2024-05-06 23:39:04', 4),
(2, 1, '2024-05-06 23:39:04', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

DROP TABLE IF EXISTS `enderecos`;
CREATE TABLE IF NOT EXISTS `enderecos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nivel_servico_envio` varchar(20) NOT NULL,
  `endereco_envio_linha1` varchar(50) NOT NULL,
  `endereco_envio_linha2` varchar(50) DEFAULT NULL,
  `endereco_envio_linha3` varchar(50) DEFAULT NULL,
  `cidade_envio` varchar(50) NOT NULL,
  `estado_envio` char(2) NOT NULL,
  `codigo_postal_envio` varchar(10) NOT NULL,
  `pais_envio` varchar(30) NOT NULL,
  `id_cliente` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`id`, `nivel_servico_envio`, `endereco_envio_linha1`, `endereco_envio_linha2`, `endereco_envio_linha3`, `cidade_envio`, `estado_envio`, `codigo_postal_envio`, `pais_envio`, `id_cliente`) VALUES
(1, 'Express', 'Rua das Palmeiras', '', 'Casa 1', 'São Paulo', 'SP', 'Brasil', '01234', 1),
(2, 'Standard', 'Rua dos Ipês', '', 'Apartamento 2', 'Rio de Janeiro', 'RJ', 'Brasil', '45678', 1),
(3, 'Standard', 'Rua das Flores', '', 'Casa 3', 'Belo Horizonte', 'MG', 'Brasil', '98765', 1),
(4, 'Express', 'Rua das Amendoeiras', '', 'Casa 4', 'Curitiba', 'PR', 'Brasil', '65432', 2),
(5, 'Standard', 'Rua das Mangueiras', '', 'Casa 5', 'Porto Alegre', 'RS', 'Brasil', '12345', 2),
(6, 'Express', 'Rua das Palmeiras', '', 'Apartamento 6', 'Brasília', 'DF', 'Brasil', '98765', 3),
(7, 'Standard', 'Rua das Oliveiras', '', 'Casa 7', 'Salvador', 'BA', 'Brasil', '54321', 3),
(8, 'Express', 'Rua das Jabuticabeiras', '', 'Casa 8', 'Recife', 'PE', 'Brasil', '67890', 4),
(9, 'Standard', 'Rua das Acácias', '', 'Casa 9', 'Belo Horizonte', 'MG', 'Brasil', '78901', 4),
(10, 'Express', 'Rua das Laranjeiras', '', 'Casa 10', 'Goiânia', 'GO', 'Brasil', '23456', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoques`
--

DROP TABLE IF EXISTS `estoques`;
CREATE TABLE IF NOT EXISTS `estoques` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantidade` int NOT NULL,
  `id_produto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_produto` (`id_produto`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `estoques`
--

INSERT INTO `estoques` (`id`, `quantidade`, `id_produto`) VALUES
(1, 0, 1),
(2, 0, 2),
(3, 0, 3),
(4, 1, 4),
(5, 4, 5),
(6, 0, 6),
(7, 0, 7),
(8, 2, 8),
(9, 1, 9),
(10, 0, 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itempedido`
--

DROP TABLE IF EXISTS `itempedido`;
CREATE TABLE IF NOT EXISTS `itempedido` (
  `id` varchar(50) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `quantidade` int NOT NULL,
  `id_pedido` varchar(50) NOT NULL,
  `id_produto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_produto` (`id_produto`),
  KEY `id_pedido` (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `itempedido`
--

INSERT INTO `itempedido` (`id`, `valor`, `quantidade`, `id_pedido`, `id_produto`) VALUES
('ITEM001', 80.00, 2, 'XZ1Y2W3', 1),
('ITEM002', 120.00, 1, 'XZ1Y2W3', 2),
('ITEM003', 50.00, 3, 'XZ1Y2W3', 3),
('ITEM004', 25.00, 1, 'UV9T8S7', 4),
('ITEM005', 30.00, 5, 'UV9T8S7', 5),
('ITEM006', 35.00, 2, 'AB6C5D4', 6),
('ITEM007', 40.00, 4, 'AB6C5D4', 7),
('ITEM008', 45.00, 2, 'ZYXWVUT', 8),
('ITEM009', 55.00, 1, 'ZYXWVUT', 9),
('ITEM010', 60.00, 3, 'QW3E4R5', 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacoes`
--

DROP TABLE IF EXISTS `movimentacoes`;
CREATE TABLE IF NOT EXISTS `movimentacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantidade` int NOT NULL,
  `dataMovimentacao` datetime NOT NULL,
  `id_pedido` varchar(50) NOT NULL,
  `id_produto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedido_produto` (`id_pedido`,`id_produto`),
  KEY `id_produto` (`id_produto`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `movimentacoes`
--

INSERT INTO `movimentacoes` (`id`, `quantidade`, `dataMovimentacao`, `id_pedido`, `id_produto`) VALUES
(1, 2, '2024-05-06 23:39:04', 'XZ1Y2W3', 1),
(2, 1, '2024-05-06 23:39:04', 'XZ1Y2W3', 2),
(3, 3, '2024-05-06 23:39:04', 'XZ1Y2W3', 3),
(4, 2, '2024-05-06 23:39:04', 'AB6C5D4', 6),
(5, 4, '2024-05-06 23:39:04', 'AB6C5D4', 7),
(6, 3, '2024-05-06 23:39:04', 'QW3E4R5', 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` varchar(50) NOT NULL,
  `dataPedido` datetime NOT NULL,
  `dataPagamento` datetime NOT NULL,
  `moeda` varchar(3) DEFAULT NULL,
  `valorTotal` decimal(10,2) DEFAULT NULL,
  `status` enum('Pendente','Em andamento','Concluído','Faltando produto','Compra Realizada') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pendente',
  `id_cliente` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `dataPedido`, `dataPagamento`, `moeda`, `valorTotal`, `status`, `id_cliente`) VALUES
('XZ1Y2W3', '2024-04-30 00:00:00', '2024-05-01 00:00:00', 'BRL', 430.00, 'Concluído', 1),
('UV9T8S7', '2024-04-30 00:00:00', '2024-05-01 00:00:00', 'BRL', 175.00, 'Compra Realizada', 2),
('AB6C5D4', '2024-04-30 00:00:00', '2024-05-01 00:00:00', 'BRL', 230.00, 'Concluído', 3),
('ZYXWVUT', '2024-04-29 00:00:00', '2024-04-30 00:00:00', 'BRL', 145.00, 'Pendente', 4),
('QW3E4R5', '2024-04-29 00:00:00', '2024-04-30 00:00:00', 'BRL', 180.00, 'Concluído', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(190) NOT NULL,
  `SKU` varchar(16) NOT NULL,
  `UPC` varchar(13) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku_unico` (`SKU`),
  UNIQUE KEY `upc_unico` (`UPC`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `SKU`, `UPC`) VALUES
(1, 'Produto A', 'SKU123', '12345678912'),
(2, 'Produto B', 'SKU456', '123456789123'),
(3, 'Produto C', 'SKU789', '987654321987'),
(4, 'Produto D', 'SKU321', '789654123987'),
(5, 'Produto E', 'SKU654', '456789123456'),
(6, 'Produto F', 'SKU987', '789123456789'),
(7, 'Produto G', 'SKU890', '321654987321'),
(8, 'Produto H', 'SKU678', '987321654987'),
(9, 'Produto I', 'SKU012', '147258369147'),
(10, 'Produto J', 'SKU345', '963852741963');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
