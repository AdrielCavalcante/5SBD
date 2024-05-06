# TABELA CLIENTES:
```
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `telefone` varchar(190) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unico` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

# TABELA ENDEREÇOS:
```
DROP TABLE IF EXISTS `enderecos`;
CREATE TABLE IF NOT EXISTS `enderecos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nivel_servico_envio` VARCHAR(20) NOT NULL,
  `endereco_envio_linha1` VARCHAR(50) NOT NULL,
  `endereco_envio_linha2` VARCHAR(50),
  `endereco_envio_linha3` VARCHAR(50),
  `cidade_envio` VARCHAR(50) NOT NULL,
  `estado_envio` CHAR(2) NOT NULL,
  `codigo_postal_envio` VARCHAR(10) NOT NULL,
  `pais_envio` VARCHAR(30) NOT NULL,
  `id_cliente` int NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_cliente`) REFERENCES clientes(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

# TABELA PRODUTOS:
```
DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(190) NOT NULL,
  `SKU` varchar(16) NOT NULL,
  `UPC` varchar(13) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku_unico` (`SKU`),
  UNIQUE KEY `upc_unico` (`UPC`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

# TABELA ESTOQUES:
```
DROP TABLE IF EXISTS `estoques`;
CREATE TABLE IF NOT EXISTS `estoques` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantidade` int NOT NULL,
  `id_produto` int NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_produto`) REFERENCES produtos(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

# TABELA PEDIDOS:
```
DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` varchar(50) NOT NULL,
  `dataPedido` DATETIME NOT NULL,
  `dataPagamento` DATETIME NOT NULL,
  `moeda` varchar(3) NULL,
  `valorTotal` decimal(10,2) NOT NULL,
  `id_cliente` int NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_cliente`) REFERENCES clientes(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

# TABELA ITENS PEDIDOS:
```
DROP TABLE IF EXISTS `itempedido`;
CREATE TABLE IF NOT EXISTS `itempedido` (
  `id` varchar(50) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `quantidade` int NOT NULL,
  `id_pedido` varchar(50) NOT NULL,
  `id_produto` int NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_produto`) REFERENCES produtos(`id`),
  FOREIGN KEY (`id_pedido`) REFERENCES pedidos(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

# TABELA MOVIMENTACOES:
```
DROP TABLE IF EXISTS `movimentacoes`;
CREATE TABLE IF NOT EXISTS `movimentacoes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `quantidade` INT NOT NULL,
  `dataMovimentacao` DATETIME NOT NULL,
  `id_pedido` VARCHAR(50) NOT NULL,
  `id_produto` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX idx_pedido_produto (`id_pedido`, `id_produto`),
  FOREIGN KEY (`id_pedido`) REFERENCES pedidos(`id`),
  FOREIGN KEY (`id_produto`) REFERENCES produtos(`id`)
);
```