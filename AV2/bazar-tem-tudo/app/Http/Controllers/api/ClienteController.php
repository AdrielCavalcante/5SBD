<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Cliente;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Clientes",
 *     description="Endpoints para gerenciar clientes"
 * )
 */
class ClienteController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/clientes",
     *     summary="Lista todos os clientes",
     *     tags={"Clientes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de clientes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nome", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Cliente::all();
    }

    /**
     * @OA\Post(
     *     path="/api/clientes",
     *     summary="Criar um novo cliente",
     *     tags={"Clientes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="telefone", type="string"),
     *             @OA\Property(property="cpf", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="telefone", type="string"),
     *             @OA\Property(property="cpf", type="string"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $cliente = Cliente::create($request->all());
        return response()->json($cliente, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/clientes/{id}",
     *     summary="Buscar cliente pelo ID",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="telefone", type="string"),
     *             @OA\Property(property="cpf", type="string"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return Cliente::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/clientes/{id}",
     *     summary="Atualizar um cliente pelo ID",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="telefone", type="string"),
     *             @OA\Property(property="cpf", type="string"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="telefone", type="string"),
     *             @OA\Property(property="cpf", type="string"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());
        return response()->json($cliente, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/clientes/{id}",
     *     summary="Deletar um cliente pelo ID",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Cliente deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();
        return response()->json(null, 204);
    }
}
