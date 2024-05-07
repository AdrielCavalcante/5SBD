# ExisteEstoqueSuficienteParaPedido - Retorna se tem ou não no estoque

```sql
DELIMITER //

CREATE PROCEDURE ExisteEstoqueSuficienteParaPedido(IN pedido_id VARCHAR(50))
BEGIN
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
END //

DELIMITER ;
```

# AtualizarEstoqueEInserirMovimentacao - Atualiza o estoque e insere em movimentação e Muda o status do pedido
```sql
DELIMITER //

CREATE PROCEDURE AtualizarEstoqueEInserirMovimentacao(IN pedido_id VARCHAR(50))
BEGIN
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
    itenspedido_loop: LOOP
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
END //

DELIMITER ;
```

# Realiza compras de produtos:
```sql
DELIMITER //

CREATE PROCEDURE RealizarCompra(IN pedido_id VARCHAR(50))
BEGIN
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
        itenspedido_loop: LOOP
            FETCH cur_itenspedido INTO produto_id, quantidade_pedido;
            IF done THEN
                LEAVE itenspedido_loop;
            END IF;
            
            -- Verifica se há estoque suficiente para o produto
            SELECT quantidade INTO quantidade_estoque
            FROM estoques
            WHERE id_produto = produto_id;
            
            -- Calcula a quantidade em falta
            SET quantidade_em_falta = GREATEST(quantidade_pedido - COALESCE(quantidade_estoque, 0), 0);
            
            -- Se houver falta de estoque, insere na tabela de compras
            IF quantidade_em_falta > 0 THEN
                INSERT INTO compras (id_produto, quantidade, data_compra)
                VALUES (produto_id, quantidade_em_falta, NOW());
            END IF;
        END LOOP itenspedido_loop;
        
        CLOSE cur_itenspedido;
    END IF;
END //

DELIMITER ;

```

# Processar pedidos:
```sql
DELIMITER //

CREATE PROCEDURE ProcessarPedidos()
BEGIN
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
            -- Atualiza o status do pedido para "Faltando produto"
            UPDATE pedidos SET status = 'Faltando produto' WHERE id = id_pedido_atual;
            -- Realiza a compra se algum produto estiver em falta
            CALL RealizarCompra(id_pedido_atual);
            -- Atualiza o status do pedido para "Compra realizada"
            UPDATE pedidos SET status = 'Compra realizada' WHERE id = id_pedido_atual;
        END IF;
    END LOOP pedido_loop;
    
    CLOSE cur_pedidos;
END //

DELIMITER ;
```

# Executador de todas procedures
```sql
DELIMITER //

CREATE PROCEDURE CriarOperacoes()
BEGIN

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
END //

DELIMITER ;
```