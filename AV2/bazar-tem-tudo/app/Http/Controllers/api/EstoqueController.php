<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Estoque;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Estoques",
 *     description="Endpoints para gerenciar estoques"
 * )
 */
class EstoqueController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/estoques",
     *     summary="Lista todos os estoques",
     *     tags={"Estoques"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de estoques",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="quantidade", type="integer"),
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
        return Estoque::all();
    }

    /**
     * @OA\Post(
     *     path="/api/estoques",
     *     summary="Adicionar um novo item ao estoque",
     *     tags={"Estoques"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item adicionado ao estoque com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $estoque = Estoque::create($request->all());
        return response()->json($estoque, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/estoques/{id}",
     *     summary="Buscar item do estoque pelo ID",
     *     tags={"Estoques"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do item do estoque",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item do estoque encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item do estoque não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return Estoque::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/estoques/{id}",
     *     summary="Atualizar um item do estoque pelo ID",
     *     tags={"Estoques"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do item do estoque",
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
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item do estoque atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="quantidade", type="integer"),
     *             @OA\Property(property="id_produto", type="integer"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item do estoque não encontrado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $estoque = Estoque::findOrFail($id);
        $estoque->update($request->all());
        return response()->json($estoque, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/estoques/{id}",
     *     summary="Remover um item do estoque pelo ID",
     *     tags={"Estoques"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do item do estoque",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Item do estoque removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item do estoque não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $estoque = Estoque::findOrFail($id);
        $estoque->delete();
        return response()->json(null, 204);
    }
}
