# InserindoDadosClientesComEndereco:

```sql
DELIMITER //

CREATE PROCEDURE InserindoDadosClientesComEndereco()
BEGIN

    INSERT INTO clientes (nome, email, telefone, cpf)
    SELECT DISTINCT
        ca.`buyer-name`, 
        ca.`buyer-email`, 
        ca.`buyer-phone-number`, 
        ca.cpf
    FROM carga ca
    LEFT JOIN clientes cl ON (cl.`email` = ca.`buyer-email`)
    WHERE cl.email IS NULL;

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
END//

DELIMITER ;
```

# InserindoDadosProdutosComEstoque:

```sql
DELIMITER //

CREATE PROCEDURE InserindoDadosProdutosComEstoque()
BEGIN

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

END//

DELIMITER ;
```

# InserindoDadosPedidos:

```sql
DELIMITER //

CREATE PROCEDURE InserindoDadosPedidos()
BEGIN

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
END//

DELIMITER ;
```

# Atualizando valorTotal em Pedido:
### Essa procedure é pra ser chamada dentro da Inserir dados Itens pedidos

```sql
DELIMITER //

CREATE PROCEDURE AtualizarValorTotalPedido()
BEGIN

    UPDATE pedidos pe SET valorTotal = (
        SELECT SUM(ip.valor * ip.quantidade) 
        FROM itempedido ip
        WHERE ip.id_pedido = pe.id
    );

END//

DELIMITER ;
```

# InserindoDadosItensPedidos:

```sql
DELIMITER //

CREATE PROCEDURE InserindoDadosItensPedidos()
BEGIN

    INSERT INTO itempedido (id, valor, quantidade, id_pedido, id_produto)
    SELECT
        ca.`order-item-id`,
        ca.`item-price`,
        ca.`quantity-purchased`,
        (SELECT id FROM pedidos WHERE pedidos.id = ca.`order-id`),
        (SELECT id FROM produtos WHERE produtos.SKU = ca.`sku`)
    FROM carga ca;

    CALL AtualizarValorTotalPedido();
END//

DELIMITER ;
```