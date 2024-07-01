<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Endereco;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\ItemPedido;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Integração API",
 *     description="Integra com serviços externos"
 * )
 */
class IntegrationService
{

    public function importarDadosCarga()
    {
        // URL do JSON
        $url = 'http://localhost:3000/carga';

        // Obtém o conteúdo do JSON
        $json = file_get_contents($url);

        // Decodifica o JSON para um array PHP
        $data = json_decode($json, true);

        foreach ($data as $order) {
            // Verifica se o item do pedido já foi adicionado anteriormente
            $itemPedidoExistente = ItemPedido::where('id', $order['order-item-id'])->exists();

            if ($itemPedidoExistente) {
                continue;
            }
            
            // Verifica se o produto já existe pelo SKU ou UPC
            $produto = Produto::where('sku', $order['sku'])->orWhere('upc', $order['upc'])->first();

            if ($produto) {
                // Verifica se o cliente já existe pelo CPF ou e-mail
                $cliente = Cliente::where('cpf', $order['cpf'])->orWhere('email', $order['buyer-email'])->first();

                if (!$cliente) {
                    // Insere o cliente se não existir
                    $cliente = new Cliente();
                    $cliente->nome = $order['buyer-name'];
                    $cliente->email = $order['buyer-email'];
                    $cliente->telefone = $order['buyer-phone-number'];
                    $cliente->cpf = $order['cpf'];
                    $cliente->save();
                }

                // Cria um array com os dados do endereço
                $enderecoData = [
                    'nivel_servico_envio' => $order['ship-service-level'],
                    'endereco_envio_linha1' => $order['ship-address-1'],
                    'endereco_envio_linha2' => $order['ship-address-2'],
                    'endereco_envio_linha3' => $order['ship-address-3'],
                    'cidade_envio' => $order['ship-city'],
                    'estado_envio' => $order['ship-state'],
                    'codigo_postal_envio' => $order['ship-postal-code'],
                    'pais_envio' => $order['ship-country'],
                    'id_cliente' => $cliente->id,
                ];
    
                // Verifica se o endereço já existe com os mesmos dados
                $endereco = Endereco::where($enderecoData)->first();
    
                // Se o endereço não existir, cria um novo
                if (!$endereco) {
                    $endereco = Endereco::create($enderecoData);
                }

                // Verifica se o pedido já existe na base de dados
                $pedido = Pedido::where('id', $order['order-id'])->first();

                // Não achou pedido
                if(!$pedido) {
                    // Insere o pedido
                    $pedido = new Pedido();
                    $pedido->id = $order['order-id'];
                    $pedido->dataPedido = $order['purchase-date'];
                    $pedido->dataPagamento = $order['payments-date'];
                    $pedido->moeda = $order['currency'];
                    $pedido->valorTotal = 0;
                    $pedido->status = 'Pendente'; // Defina o status inicial do pedido conforme necessário
                    $pedido->id_cliente = $cliente->id;
                    $pedido->save();
                }
                Log::info('Pedido '. $pedido .' criado com sucesso.');
                // Insere o item do pedido
                $orderPedido = new ItemPedido();
                $orderPedido->id = $order['order-item-id'];
                $orderPedido->valor = (float) $order['item-price'];
                $orderPedido->quantidade = (int) $order['quantity-purchased'];
                $orderPedido->id_pedido = $pedido->id;
                $orderPedido->id_produto = $produto->id;
                $orderPedido->save();

                // Atualizar o valor total do pedido
                $pedido->valorTotal += $orderPedido->valor * $orderPedido->quantidade;
                $pedido->save();
            } else {
                // Log de erro
                Log::error('Produto não encontrado: SKU ' . $order['sku'] . ' ou UPC ' . $order['upc']);
            }
        }

        // Log de informações
        Log::info('Importação de dados da carga concluída com sucesso.');

        // Pode retornar informações adicionais se necessário
        return ['message' => 'Importação de dados da carga concluída com sucesso.'];
    }

    /**
     * Envia um pedido para a API de entrega.
     *
     * @OA\Post(
     *     path="/api/pedidos/{id}/enviar-para-entrega",
     *     tags={"Integração API"},
     *     summary="Envia um pedido para a API de entrega",
     *     operationId="enviarPedidoParaEntrega",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido a ser enviado",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido enviado com sucesso para a API de entrega",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="mensagem", type="string", example="O pedido está sendo processado e será enviado em breve."),
     *             @OA\Property(property="diasEstimadosEntrega", type="integer", example=5),
     *             @OA\Property(property="codigoRastreamento", type="string", example="ABC123"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Pedido não encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao enviar pedido para a API de entrega",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao enviar pedido para a API de entrega.")
     *         )
     *     ),
     * )
     */
    public function enviarPedidoParaEntrega($id)
    {
        // URL da API de entrega
        $url = 'http://localhost:3000/entrega';

        $pedido = Pedido::find($id);

        if (!$pedido) {
            return response()->json(['success' => false, 'message' => 'Pedido não encontrado.'], 404);
        }

        // Dados do pedido para enviar
        $data = [
            'id' => $id,
            'nome' => $pedido->cliente->nome, 
            'cpf' => $pedido->cliente->cpf, 
            'endereco' => [
                'cep' => $pedido->cliente->enderecos[0]->codigo_postal_envio, 
                'logradouro' => $pedido->cliente->enderecos[0]->endereco_envio_linha1, 
                'cidade' => $pedido->cliente->enderecos[0]->cidade_envio, 
                'estado' => $pedido->cliente->enderecos[0]->estado_envio, 
            ],
            'itens' => [],
        ];

        // Adiciona os itens do pedido
        foreach ($pedido->itensPedidos as $item) {
            $data['itens'][] = [
                'produto' => $item->produto->nome, 
                'quantidade' => $item->quantidade,
                'precoUnitario' => $item->valor, 
            ];
        }

        try {
            // Envia a requisição POST para a API de entrega usando o HTTP Client do Laravel
            $response = Http::post($url, $data);

            // Verifica o status da resposta
            if ($response->successful()) {
                // Atualiza o status do pedido para "Entrega"
                $pedido->status = 'Entrega';
                $pedido->save();

                $responseData = $response->json();

                $mensagem = $responseData['mensagem'];
                $diasEstimadosEntrega = $responseData['diasEstimadosEntrega'];
                $codigoRastreamento = $responseData['codigoRastreamento'];

                Log::info('Pedido '.$pedido->id.' enviado com sucesso para a API de entrega.');

                return [
                    'success' => true,
                    'mensagem' => $mensagem,
                    'diasEstimadosEntrega' => $diasEstimadosEntrega,
                    'codigoRastreamento' => $codigoRastreamento,
                ];
            } else {
                Log::error('Erro ao enviar pedido '.$pedido->id.' para a API de entrega: '.$response->status());
                return ['success' => false, 'message' => 'Erro ao enviar pedido para a API de entrega.'];
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao enviar pedido '.$pedido->id.' para a API de entrega: '.$e->getMessage());
            return ['success' => false, 'message' => 'Exceção ao enviar pedido para a API de entrega.'];
        }
    }

    /**
     * @OA\Get(
     *     path="/api/statusEntrega",
     *     tags={"Integração API"},
     *     summary="Consulta o status da entrega de um pedido",
     *     operationId="consultarStatusEntrega",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID do pedido a ser consultado",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="codigoRastreamento",
     *         in="query",
     *         description="Código de rastreamento do pedido a ser consultado",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status da entrega consultado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Seu pedido está a caminho e chegará hoje!"),
     *             @OA\Property(property="diasFaltando", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="ID do pedido ou código de rastreamento não fornecido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="É necessário fornecer o ID do pedido ou o código de rastreamento")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido ou código de rastreamento não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pedido ou código de rastreamento não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao buscar status de entrega",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao buscar status de entrega")
     *         )
     *     )
     * )
     */
    public function consultarStatusEntrega(Request $request)
    {
        $url = 'http://localhost:3000/statusEntrega';

        // Valida a requisição para garantir que o ID ou o código de rastreamento estejam presentes
        $request->validate([
            'id' => 'nullable|string',
            'codigoRastreamento' => 'nullable|string',
        ]);

        // Verifica se ao menos um dos parâmetros foi fornecido
        if (!$request->has('id') && !$request->has('codigoRastreamento')) {
            return response()->json(['message' => 'É necessário fornecer o ID do pedido ou o código de rastreamento'], 400);
        }

        // Envia a requisição GET para o endpoint externo
        $response = Http::get($url, [
            'id' => $request->query('id'),
            'codigoRastreamento' => $request->query('codigoRastreamento'),
        ]);

        // Retorna a resposta do endpoint externo
        return response()->json($response->json(), $response->status());
    }
}
