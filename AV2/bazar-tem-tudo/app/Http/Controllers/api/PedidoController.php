<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Pedido;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Pedidos",
 *     description="Endpoints para gerenciar pedidos"
 * )
 */
class PedidoController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/pedidos",
     *     summary="Lista todos os pedidos",
     *     tags={"Pedidos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="dataPedido", type="string", format="date"),
     *                 @OA\Property(property="dataPagamento", type="string", format="date"),
     *                 @OA\Property(property="moeda", type="string"),
     *                 @OA\Property(property="valorTotal", type="number", format="float"),
     *                 @OA\Property(property="status", type="string", enum={"Pendente", "Em andamento", "Concluído", "Faltando produto", "Compra Realizada"}),
     *                 @OA\Property(property="id_cliente", type="integer"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Pedido::all();
    }

    /**
     * @OA\Post(
     *     path="/api/pedidos",
     *     summary="Criar um novo pedido",
     *     tags={"Pedidos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="dataPedido", type="string", format="date", example="2024-06-01"),
     *             @OA\Property(property="dataPagamento", type="string", format="date", example="2024-06-02"),
     *             @OA\Property(property="moeda", type="string", example="BRL"),
     *             @OA\Property(property="valorTotal", type="number", format="float"),
     *             @OA\Property(property="status", type="string", enum={"Pendente", "Em andamento", "Concluído", "Faltando produto", "Compra Realizada"}),
     *             @OA\Property(property="id_cliente", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="dataPedido", type="string", format="date", example="2024-06-01"),
     *             @OA\Property(property="dataPagamento", type="string", format="date", example="2024-06-02"),
     *             @OA\Property(property="moeda", type="string", example="BRL"),
     *             @OA\Property(property="valorTotal", type="number", format="float"),
     *             @OA\Property(property="status", type="string", enum={"Pendente", "Em andamento", "Concluído", "Faltando produto", "Compra Realizada"}),
     *             @OA\Property(property="id_cliente", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $pedido = Pedido::create($request->all());
        return response()->json($pedido, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/pedidos/{id}",
     *     summary="Buscar pedido pelo ID",
     *     tags={"Pedidos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do pedido",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="dataPedido", type="string", format="date"),
     *             @OA\Property(property="dataPagamento", type="string", format="date"),
     *             @OA\Property(property="moeda", type="string"),
     *             @OA\Property(property="valorTotal", type="number", format="float"),
     *             @OA\Property(property="status", type="string", enum={"Pendente", "Em andamento", "Concluído", "Faltando produto", "Compra Realizada"}),
     *             @OA\Property(property="id_cliente", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return Pedido::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/pedidos/{id}",
     *     summary="Atualizar um pedido pelo ID",
     *     tags={"Pedidos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do pedido",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="dataPedido", type="string", format="date"),
     *             @OA\Property(property="dataPagamento", type="string", format="date"),
     *             @OA\Property(property="moeda", type="string"),
     *             @OA\Property(property="valorTotal", type="number", format="float"),
     *             @OA\Property(property="status", type="string", enum={"Pendente", "Em andamento", "Concluído", "Faltando produto", "Compra Realizada"}),
     *             @OA\Property(property="id_cliente", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="dataPedido", type="string", format="date", example="2024-06-01"),
     *             @OA\Property(property="dataPagamento", type="string", format="date", example="2024-06-02"),
     *             @OA\Property(property="moeda", type="string", example="BRL"),
     *             @OA\Property(property="valorTotal", type="number", format="float"),
     *             @OA\Property(property="status", type="string", enum={"Pendente", "Em andamento", "Concluído", "Faltando produto", "Compra Realizada"}),
     *             @OA\Property(property="id_cliente", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->update($request->all());
        return response()->json($pedido, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/pedidos/{id}",
     *     summary="Deletar um pedido pelo ID",
     *     tags={"Pedidos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do pedido",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Pedido deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->delete();
        return response()->json(null, 204);
    }
}
