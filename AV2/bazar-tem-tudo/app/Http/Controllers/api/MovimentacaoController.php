<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Movimentacao;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Movimentações",
 *     description="Endpoints para gerenciar movimentações"
 * )
 */
class MovimentacaoController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/movimentacoes",
     *     summary="Lista todas as movimentações",
     *     tags={"Movimentações"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de movimentações",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="quantidade", type="integer"),
     *                 @OA\Property(property="dataMovimentacao", type="string"),
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
        return Movimentacao::all();
    }

    /**
     * @OA\Post(
     *     path="/api/movimentacoes",
     *     summary="Registrar uma nova movimentação",
     *     tags={"Movimentações"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="dataMovimentacao", type="string"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movimentação registrada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="dataMovimentacao", type="string", example="2024-06-02"),
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
        $movimentacao = Movimentacao::create($request->all());
        return response()->json($movimentacao, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/movimentacoes/{id}",
     *     summary="Buscar movimentação pelo ID",
     *     tags={"Movimentações"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da movimentação",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movimentação encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="dataMovimentacao", type="string"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movimentação não encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        return Movimentacao::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/movimentacoes/{id}",
     *     summary="Atualizar uma movimentação pelo ID",
     *     tags={"Movimentações"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da movimentação",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="dataMovimentacao", type="string"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movimentação atualizada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="dataMovimentacao", type="string"),
     *             @OA\Property(property="id_pedido", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movimentação não encontrada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $movimentacao = Movimentacao::findOrFail($id);
        $movimentacao->update($request->all());
        return response()->json($movimentacao, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/movimentacoes/{id}",
     *     summary="Remover uma movimentação pelo ID",
     *     tags={"Movimentações"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da movimentação",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Movimentação removida com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movimentação não encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $movimentacao = Movimentacao::findOrFail($id);
        $movimentacao->delete();
        return response()->json(null, 204);
    }
}
