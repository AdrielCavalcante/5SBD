const express = require('express');
const mysql = require('mysql2');
const bodyParser = require('body-parser');

const app = express();
const port = 3000;

app.use(bodyParser.json());

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

app.post('/entrega', (req, res) => {
  const { id, nome, cpf, endereco, itens } = req.body;

  if (!nome || !cpf || !endereco || !itens) {
      return res.status(400).send({ message: 'Os campos não estão certos!' });
  }

  const diasEntrega = calcularTempoEntrega(endereco.cep);

  const codigoRastreamento = gerandoCodigoEnvio();

  const dadosEntrega = {
    id: id,
    nome_cliente: nome,
    cpf_cliente: cpf,
    endereco_logradouro: endereco.logradouro,
    endereco_cidade: endereco.cidade,
    endereco_estado: endereco.estado,
    endereco_cep: endereco.cep,
    dias_estimados_entrega: diasEntrega,
    codigo_rastreamento: codigoRastreamento
  };

  // Insere os dados na tabela correio
  connection.query('INSERT INTO correio SET ?', dadosEntrega, (err, results) => {
    if (err) {
      console.error('Erro ao inserir dados na tabela correio:', err);
      return res.status(500).send({ error: 'Erro ao inserir dados na tabela correio' });
    }

    // Prepara os dados dos itens do pedido para inserção
    const itensPedido = itens.map(item => [
      id,
      item.produto,
      item.quantidade,
      item.precoUnitario
    ]);

    // Insere os itens do pedido na tabela correio_itens
    const queryItens = 'INSERT INTO correio_itens (id_correio, produto, quantidade, preco_unitario) VALUES ?';
    connection.query(queryItens, [itensPedido], (errItens) => {
      if (errItens) {
        console.error('Erro ao inserir itens do pedido:', errItens);
        return res.status(500).send({ error: 'Erro ao inserir itens do pedido' });
      }

      // Salva a response da API em um arquivo de log separado
      const logMessage = `Response da API de entrega:${JSON.stringify(results)}${'---'}`;
      console.log(logMessage);

      res.status(200).send({
        mensagem: 'O pedido está sendo processado e será enviado em breve.',
        diasEstimadosEntrega: diasEntrega,
        codigoRastreamento: codigoRastreamento,
        detalhesPedidos: {
          nome,
          cpf,
          endereco,
          itens,
        }
      });
    });
  });
});

// Endpoint /statusEntrega
app.get('/statusEntrega', (req, res) => {
  const { id, codigoRastreamento } = req.query;

  if (!id && !codigoRastreamento) {
    return res.status(400).send({ message: 'É necessário fornecer o ID do pedido ou o código de rastreamento' });
  }

  let query;
  let queryParams;

  if (id) {
    query = `
      SELECT dias_estimados_entrega
      FROM correio
      WHERE id = ?
    `;
    queryParams = [id];
  } else if (codigoRastreamento) {
    query = `
      SELECT dias_estimados_entrega
      FROM correio
      WHERE codigo_rastreamento = ?
    `;
    queryParams = [codigoRastreamento];
  }

  // Executa a consulta no banco de dados
  connection.query(query, queryParams, (err, results) => {
    if (err) {
      console.error('Erro ao buscar status de entrega:', err);
      return res.status(500).send({ error: 'Erro ao buscar status de entrega' });
    }

    if (results.length === 0) {
      return res.status(404).send({ message: 'Pedido ou código de rastreamento não encontrado' });
    }

    const { dias_estimados_entrega } = results[0];
    let message;
    let diasFaltando;

    if( dias_estimados_entrega === 1) {
      message = 'Seu pedido está a caminho e chegará hoje!';
    } else if (dias_estimados_entrega === 3) {
      message = 'Seu pedido está a caminho e chegará em breve.';
    } else {
      // random entre 1 e 6
      const random = Math.floor(Math.random() * 6) + 1;
      diasFaltando = dias_estimados_entrega - random;
      message = `Seu pedido está a caminho e chegará em ${diasFaltando} dias.`;
    }

    res.status(200).send({
      message,
      diasFaltando,
    });
  });
});


function calcularTempoEntrega(cep) {
  // Simulando entrega de acordo com o CEP
  if (cep >= '20000-001' && cep <= '23799-999') {
      return 1; // Cidade do Rio de Janeiro
  } else if (cep >= '20000-000' && cep <= '28999-999') {
      return 3; // Estado do Rio de Janeiro
  } else {
      return 7; // É além do Rio de Janeiro
  }
}

function gerandoCodigoEnvio() {
  // Basic simulation of tracking code generation
  return Math.random().toString(36).substr(2, 9); // Example: Generates a random 9-character alphanumeric string
}


// Inicia o servidor
app.listen(port, () => {
  console.log(`Servidor rodando em http://localhost:${port}`);
});
