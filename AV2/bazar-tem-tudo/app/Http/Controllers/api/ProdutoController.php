<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Produto;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Produtos",
 *     description="Endpoints para gerenciar produtos"
 * )
 */
class ProdutoController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/produtos",
     *     summary="Lista todos os produtos",
     *     tags={"Produtos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nome", type="string"),
     *                 @OA\Property(property="SKU", type="string"),
     *                 @OA\Property(property="UPC", type="string"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Produto::all();
    }

    /**
     * @OA\Post(
     *     path="/api/produtos",
     *     summary="Criar um novo produto",
     *     tags={"Produtos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="SKU", type="string"),
     *             @OA\Property(property="UPC", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nome", type="string"),
     *                 @OA\Property(property="SKU", type="string"),
     *                 @OA\Property(property="UPC", type="string"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $produto = Produto::create($request->all());
        return response()->json($produto, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/produtos/{id}",
     *     summary="Buscar produto pelo ID",
     *     tags={"Produtos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nome", type="string"),
     *                 @OA\Property(property="SKU", type="string"),
     *                 @OA\Property(property="UPC", type="string"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return Produto::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/produtos/{id}",
     *     summary="Atualizar um produto pelo ID",
     *     tags={"Produtos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nome", type="string"),
     *                 @OA\Property(property="SKU", type="string"),
     *                 @OA\Property(property="UPC", type="string"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nome", type="string"),
     *                 @OA\Property(property="SKU", type="string"),
     *                 @OA\Property(property="UPC", type="string"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);
        $produto->update($request->all());
        return response()->json($produto, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/produtos/{id}",
     *     summary="Deletar um produto pelo ID",
     *     tags={"Produtos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Produto deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();
        return response()->json(null, 204);
    }
}
