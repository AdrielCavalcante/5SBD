# inserindo clientes
INSERT INTO cliente (nome, email, endereco, UF, Pais, CEP)
SELECT DISTINCT
    ca.NomeComprador, 
    ca.email, 
    ca.endereco, 
    ca.UF, 
    ca.Pais, 
    ca.CEP
FROM carga ca
LEFT JOIN cliente cl ON (cl.email = ca.email)
WHERE cl.email IS NULL;

# inserindo pedidos
INSERT INTO pedido (id, dataPedido, frete, valorTotal, idCliente)
SELECT
    ca.CodigoPedido,
    ca.DataPedido,
    ca.Frete,
    0,
    (SELECT id FROM cliente cl WHERE cl.email = ca.email)
FROM carga ca
INNER JOIN cliente cl ON (cl.email = ca.email)
WHERE NOT EXISTS (
    SELECT 1
    FROM pedido p
    WHERE p.id = ca.CodigoPedido
)
GROUP BY ca.CodigoPedido;

# inserindo produtos
INSERT INTO produto (nome, SKU, UPC, valor)
SELECT
    ca.NomeProduto,
    ca.SKU,
    ca.UPC,
    ca.Valor
FROM carga ca
LEFT JOIN produto p ON (ca.SKU = p.SKU OR ca.UPC = p.UPC);

# inserindo itens pedido
INSERT INTO itempedido (quantidade, idPedido, idProduto)
SELECT
    ca.QTD,
    (SELECT id FROM pedido pe WHERE pe.id = ca.CodigoPedido),
    (SELECT id FROM produto po WHERE po.SKU = ca.SKU)
FROM carga ca;

# inserindo estoques
INSERT INTO estoque (quantidade, idProduto)
SELECT 
    ca.QTD,
    (SELECT id FROM produto po WHERE po.SKU = ca.SKU)
FROM carga ca;

# atualizando valorTotal em Pedido (Não considera o frete na conta)
UPDATE pedido pe SET valorTotal = (
    SELECT SUM(po.valor * ip.quantidade) 
    FROM itempedido ip
    INNER JOIN produto po ON (ip.idProduto = po.id) 
    WHERE ip.idPedido = pe.id       
);