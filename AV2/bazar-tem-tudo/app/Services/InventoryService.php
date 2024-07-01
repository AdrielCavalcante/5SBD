<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Models\Compra;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\Movimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function ProcessarPedidosComprados()
    {
        $this->atualizarEstoque();

        $this->processarPedidos($order="old");
    }

    public function processarPedidos($order = null)
    {
        if($order == "old") {
            // Obtém os pedidos na ordem de criação - Mais antigo primeiro
            $pedidos = Pedido::orderBy('created_at')->get();
        } else {
            // Obtém os pedidos em ordem decrescente de valor total - Maior valor primeiro
            $pedidos = Pedido::orderByDesc('valorTotal')->get();
        }

        foreach ($pedidos as $pedido) {
            $pedidoId = $pedido->id;

            if($pedido->status == 'Concluído') {
                continue;
            }

            if($pedido->status == 'Pendente') {
                // Processando
                Pedido::where('id', $pedidoId)->update(['status' => 'Em andamento']);
            }

            // Verifica se há estoque suficiente para o pedido atual
            $estoqueSuficiente = $this->existeEstoqueSuficienteParaPedido($pedidoId);

            // Processa o pedido conforme o estoque disponível
            if ($estoqueSuficiente) {
                // Atualiza o estoque e insere a movimentação
                $this->atualizarEstoqueEInserirMovimentacao($pedidoId);

                // Atualiza o status do pedido para "Concluído"
                Pedido::where('id', $pedidoId)->update(['status' => 'Concluído']);
            } else {
                // Atualiza o status do pedido para "Faltando produto"
                Pedido::where('id', $pedidoId)->update(['status' => 'Faltando produto']);

                // Realiza a compra se algum produto estiver em falta
                $this->realizarCompra($pedidoId);

                // Atualiza o status do pedido para "Compra realizada"
                Pedido::where('id', $pedidoId)->update(['status' => 'Compra Realizada']);
            }
        }
    }

    private function existeEstoqueSuficienteParaPedido($pedidoId)
    {
        // Pega todos os itens do pedido com ID do pedido
        $itensPedido = ItemPedido::where('id_pedido', $pedidoId)->get();
        $estoqueSuficiente = true;

        foreach ($itensPedido as $item) {
            // Pegando estoque quantidade total do produto
            $quantidadeEstoque = Estoque::where('id_produto', $item->id_produto)->sum('quantidade');
            if ($quantidadeEstoque < $item->quantidade) {
                $estoqueSuficiente = false;
                continue;
            }
        }
        Log::info('Retorno do estoque: '. $estoqueSuficiente .' - (existeEstoqueSuficienteParaPedido)');
        return $estoqueSuficiente;
    }

    private function atualizarEstoqueEInserirMovimentacao($pedidoId)
    {
        // Obtem todos os itens do pedido com ID do pedido
        $itensPedido = ItemPedido::where('id_pedido', $pedidoId)->get();
        $dataAtual = now();

        foreach ($itensPedido as $item) {
            $produtoId = $item->id_produto;
            $quantidadePedido = $item->quantidade;

            // Atualiza a quantidade no estoque
            Estoque::where('id_produto', $produtoId)
                ->decrement('quantidade', $quantidadePedido);

            // Insere na tabela de movimentações
            Movimentacao::create([
                'quantidade' => $quantidadePedido,
                'dataMovimentacao' => $dataAtual,
                'id_pedido' => $pedidoId,
                'id_produto' => $produtoId,
            ]);
        }

        Log::info('Pedido '. $pedidoId .' foi concluido - (atualizarEstoqueEInserirMovimentacao)');
        return true;
    }

    private function realizarCompra($pedidoId)
    {
        try {
            // Verifica se o pedido está com status "Faltando produto"
            $pedido = Pedido::findOrFail($pedidoId);
            if ($pedido->status !== 'Faltando produto') {
                Log::info('Pedido não está marcado como faltando produto - (realizarCompra)');
                return response()->json(['message' => 'Pedido não está marcado como faltando produto'], 400);
            }

            // Obtém os itens do pedido
            $itensPedido = ItemPedido::where('id_pedido', $pedidoId)->get();

            foreach ($itensPedido as $itemPedido) {
                $produtoId = $itemPedido->id_produto;
                $quantidadePedido = $itemPedido->quantidade;

                // Verifica se há estoque suficiente para o produto
                $estoque = Estoque::where('id_produto', $produtoId)->first();

                // Calcula a quantidade em falta
                $quantidadeEmFalta = max($quantidadePedido - $estoque->quantidade, 0);
            
                // Se houver falta de estoque, insere na tabela de compras
                if ($quantidadeEmFalta > 0) {
                    // Cria uma nova compra se não existir
                    Compra::create([
                        'data_compra' => now(),
                        'quantidade' => $quantidadeEmFalta,
                        'id_produto' => $produtoId,
                        'comprado' => false,
                    ]);
                }
            }

            Log::error('Foi comprado '. $quantidadeEmFalta .' do produto '. $produtoId .' - (realizarCompra)');
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao realizar compra: ' . $e->getMessage());
            return false;
        }
    }

    private function atualizarEstoque()
    {
        try {
            // Obtém todas as compras que não foram processadas
            $compras = Compra::where('comprado', false)->get();

            foreach ($compras as $compra) {
                // Verifica se o estoque para o produto já existe
                $estoque = Estoque::where('id_produto', $compra->id_produto)->first();

                if ($estoque) {
                    // Incrementa a quantidade no estoque existente
                    $estoque->quantidade += $compra->quantidade;

                } else {
                    // Cria um novo registro de estoque se não existir
                    $estoque = new Estoque();
                    $estoque->id_produto = $compra->id_produto;
                    $estoque->quantidade = $compra->quantidade;
                }

                // Salva o registro de estoque
                $estoque->save();

                // Marca a compra como processada
                $compra->comprado = true;
                $compra->save();

                // Log de atualização de estoque
                Log::info('Estoque atualizado para o produto ' . $compra->id_produto . ' com quantidade ' . $estoque->quantidade);
            }

            return ['message' => 'Estoque atualizado com sucesso'];
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar estoque: ' . $e->getMessage());
            return ['message' => 'Erro ao atualizar estoque', 'error' => $e->getMessage()];
        }
    }
}