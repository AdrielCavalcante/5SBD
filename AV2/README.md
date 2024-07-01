# AV2 do Bazar tem Tudo

Em: bazar-tem-tudo, está o projeto feito em ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

## Procedimento Laravel
Vá no .env.example e altere para .env
Crie um Banco chamado: bazarlaravel
Estou usando o MySQL, a partir do PHPMYADMIN

### Instalando dependências
```sh
$ composer install
```

### Para executar o Laravel
```sh
$ php artisan serve
```

### Para rodar as migrations e Criar as tabelas
```sh
$ php artisan migrate
```

Link para acessar o Swagger, durante execução:
<http://127.0.0.1:8000/api/docs#>

##### Em app/Services, temos a lógica de integração do sistema.
- IntegrationService.php - Conecta a serviços externos como API de carga de pedidos e Entrega
- InventoryService - Lógica do processamento do pedido e movimentação de estoque

##### Em app/Console, temos a lógica que executa o CRONJOB
- no Kernel.php é configurado o comando que você deve configurar no seu CRON ou no Agendador de Tarefas
- por exemplo: `php artisan run:cron-market` - Que executa a importação dos pedidos da API e Processa os pedidos

---

## Procedimento Node
Criei um Fake JSON, para simular uma API de um marketplace como Amazon, Mercado Livre e etc.
Usei o MySQL e criei uma tabela de carga na qual poderia ser estruturado, mas como irei apenas simular a entrega do JSON, não me preocupei

Nome do banco a ser criado: bazartemtudo

Importe o arquivo carga.sql que está no diretório da AV2, para poder ser exibido o JSON

### Instalando dependências
```sh
$ npm install
```

### Rodando o Node
```sh
$ node index.js
```

Link para acessar o JSON Fake pra carga, durante execução:
<http://localhost:3000/carga>

Link para acessar o JSON Fake pra cadastrar na entrega, o pedido, durante execução:
<http://localhost:3000/entrega>

Link para acessar o JSON Fake pra consultar o status da entrega, durante execução:
<http://localhost:3000/statusEntrega>