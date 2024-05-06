# ExisteEstoqueSuficienteParaPedido:

```
DELIMITER //

CREATE PROCEDURE ExisteEstoqueSuficienteParaPedido(IN pedido_id VARCHAR(50))
BEGIN
    DECLARE estoque_suficiente BOOLEAN DEFAULT TRUE;
    DECLARE produto_id INT;
    DECLARE quantidade_pedido INT;
    DECLARE quantidade_estoque INT;
    
    -- Cursor para percorrer os itens do pedido
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
    itenspedido_loop: LOOP
        FETCH cur_itenspedido INTO produto_id, quantidade_pedido;
        IF NOT estoque_suficiente THEN
            LEAVE itenspedido_loop;
        END IF;
        
        -- Verifica se há estoque suficiente para o produto
        SELECT quantidade INTO quantidade_estoque
        FROM estoques
        WHERE id_produto = produto_id;
        
        IF quantidade_estoque < quantidade_pedido THEN
            SET estoque_suficiente = FALSE;
            LEAVE itenspedido_loop;
        END IF;
    END LOOP itenspedido_loop;
    
    CLOSE cur_itenspedido;
    
    -- Retorna TRUE se houver estoque suficiente, FALSE caso contrário
    SELECT estoque_suficiente;
END //

DELIMITER ;
```

# AtualizarEstoqueEInserirMovimentacao
```
DELIMITER //

CREATE PROCEDURE AtualizarEstoqueEInserirMovimentacao(IN pedido_id VARCHAR(50))
BEGIN
    DECLARE produto_id INT;
    DECLARE quantidade_pedido INT;
    DECLARE done INT DEFAULT FALSE;
    
    -- Cursor para percorrer os itens do pedido
    DECLARE cur_itenspedido CURSOR FOR
        SELECT id_produto, quantidade
        FROM itempedido
        WHERE id_pedido = pedido_id;
    
    -- Declaração de handler para cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Variável para armazenar a data e hora atual
    DECLARE data_atual DATETIME;
    SET data_atual = NOW();
    
    -- Inicialização
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

# Processar pedidos:
```
DELIMITER //

CREATE PROCEDURE ProcessarPedidos()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE id_pedido_atual VARCHAR(50);
    
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
        IF ExisteEstoqueSuficienteParaPedido(id_pedido_atual) THEN
            -- Atualiza o estoque e registra a movimentação
            AtualizarEstoqueEInserirMovimentacao(id_pedido_atual);
        ELSE
            -- Não há estoque suficiente para o pedido atual, passa para o próximo pedido
            LEAVE pedido_loop;
        END IF;
    END LOOP pedido_loop;
    
    CLOSE cur_pedidos;
END //

DELIMITER ;
```