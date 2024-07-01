<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Controllers\Api\EnderecoController;
use App\Http\Controllers\Api\EstoqueController;
use App\Http\Controllers\Api\ItemPedidoController;
use App\Http\Controllers\Api\MovimentacaoController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\ProdutoController;
use App\Services\IntegrationService;

Route::get('/', function () {
    return response()->json(['message' => 'API Bazar Tem Tudo', 'status' => 'Conectado']);
});

Route::post('pedidos/{id}/enviar-para-entrega', [IntegrationService::class, 'enviarPedidoParaEntrega']);

Route::apiResource('clientes', ClienteController::class);
Route::apiResource('compras', CompraController::class);
Route::apiResource('enderecos', EnderecoController::class);
Route::apiResource('estoques', EstoqueController::class);
Route::apiResource('item-pedidos', ItemPedidoController::class);
Route::apiResource('movimentacoes', MovimentacaoController::class);
Route::apiResource('pedidos', PedidoController::class);
Route::apiResource('produtos', ProdutoController::class);
