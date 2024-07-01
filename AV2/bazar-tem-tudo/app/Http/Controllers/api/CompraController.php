<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Compra;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Compras",
 *     description="Endpoints para gerenciar compras"
 * )
 */
class CompraController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/compras",
     *     summary="Lista todas as compras",
     *     tags={"Compras"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de compras",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="id_produto", type="integer"),
     *                 @OA\Property(property="quantidade", type="integer"),
     *                 @OA\Property(property="valor_unitario", type="number", format="float"),
     *                 @OA\Property(property="comprado", type="boolean"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Compra::all();
    }

    /**
     * @OA\Post(
     *     path="/api/compras",
     *     summary="Criar uma nova compra",
     *     tags={"Compras"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="data_compra", type="string", example="2024-06-29"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="comprado", type="boolean", example="false"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compra criada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="valor_unitario", type="number", format="float"),
     *             @OA\Property(property="comprado", type="boolean", example="false"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $compra = Compra::create($request->all());
        return response()->json($compra, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/compras/{id}",
     *     summary="Buscar compra pelo ID",
     *     tags={"Compras"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da compra",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compra encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="valor_unitario", type="number", format="float"),
     *             @OA\Property(property="comprado", type="boolean", example="false"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compra não encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        return Compra::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/compras/{id}",
     *     summary="Atualizar uma compra pelo ID",
     *     tags={"Compras"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da compra",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="data_compra", type="string", example="2024-06-29"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="comprado", type="boolean", example="false"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compra atualizada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="data_compra", type="string", format="date"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="comprado", type="boolean", example="false"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compra não encontrada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);
        $compra->update($request->all());
        return response()->json($compra, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/compras/{id}",
     *     summary="Deletar uma compra pelo ID",
     *     tags={"Compras"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da compra",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Compra deletada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compra não encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->delete();
        return response()->json(null, 204);
    }
}
