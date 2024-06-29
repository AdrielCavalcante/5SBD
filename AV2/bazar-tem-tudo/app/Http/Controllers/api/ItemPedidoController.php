<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\ItemPedido;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Itens de Pedido",
 *     description="Endpoints para gerenciar itens de pedidos"
 * )
 */
class ItemPedidoController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/item-pedidos",
     *     summary="Lista todos os itens de pedidos",
     *     tags={"Itens de Pedido"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de itens de pedidos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="valor", type="number", format="float"),
     *                 @OA\Property(property="quantidade", type="integer"),
     *                 @OA\Property(property="id_pedido", type="integer"),
     *                 @OA\Property(property="id_produto", type="integer"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return ItemPedido::all();
    }

    /**
     * @OA\Post(
     *     path="/api/item-pedidos",
     *     summary="Adicionar um novo item ao pedido",
     *     tags={"Itens de Pedido"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="valor", type="number", format="float"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item adicionado ao pedido com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="valor", type="number", format="float"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $itemPedido = ItemPedido::create($request->all());
        return response()->json($itemPedido, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/item-pedidos/{id}",
     *     summary="Buscar item de pedido pelo ID",
     *     tags={"Itens de Pedido"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do item de pedido",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item de pedido encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="valor", type="number", format="float"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item de pedido não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return ItemPedido::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/item-pedidos/{id}",
     *     summary="Atualizar um item de pedido pelo ID",
     *     tags={"Itens de Pedido"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do item de pedido",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="valor", type="number", format="float"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item de pedido atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="valor", type="number", format="float"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item de pedido não encontrado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $itemPedido = ItemPedido::findOrFail($id);
        $itemPedido->update($request->all());
        return response()->json($itemPedido, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/item-pedidos/{id}",
     *     summary="Remover um item de pedido pelo ID",
     *     tags={"Itens de Pedido"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do item de pedido",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Item de pedido removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item de pedido não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $itemPedido = ItemPedido::findOrFail($id);
        $itemPedido->delete();
        return response()->json(null, 204);
    }
}
