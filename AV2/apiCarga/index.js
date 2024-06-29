const express = require('express');
const mysql = require('mysql2');
const app = express();
const port = 3000;

// Configuração do banco de dados
const dbConfig = {
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'bazartemtudo'
};

// Cria a conexão com o banco de dados
const connection = mysql.createConnection(dbConfig);

connection.connect((err) => {
  if (err) {
    console.error('Erro ao conectar ao banco de dados:', err);
    return;
  }
  console.log('Conexão bem-sucedida ao banco de dados');
});

// Endpoint /carga
app.get('/carga', (req, res) => {
    const query = `
    SELECT 
      \`order-id\`, \`order-item-id\`, \`purchase-date\`, \`payments-date\`, \`buyer-email\`, 
      \`buyer-name\`, \`cpf\`, \`buyer-phone-number\`, \`sku\`, \`upc\`, \`product-name\`, 
      \`quantity-purchased\`, \`currency\`, \`item-price\`, \`ship-service-level\`, 
      \`ship-address-1\`, \`ship-address-2\`, \`ship-address-3\`, \`ship-city\`, \`ship-state\`, 
      \`ship-postal-code\`, \`ship-country\` 
    FROM \`carga\`
  `;

  connection.query(query, (err, results) => {
    if (err) {
      console.error('Erro ao executar a query:', err);
      res.status(500).json({ error: 'Erro ao buscar dados' });
      return;
    }
    res.json(results);
  });
});

// Inicia o servidor
app.listen(port, () => {
  console.log(`Servidor rodando em http://localhost:${port}`);
});
