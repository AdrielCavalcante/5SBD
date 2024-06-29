<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Endereco;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Enderecos",
 *     description="Endpoints para gerenciar endereços"
 * )
 */
class EnderecoController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/enderecos",
     *     summary="Lista todos os endereços",
     *     tags={"Enderecos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de endereços",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nivel_servico_envio", type="string", example="Express"),
     *                 @OA\Property(property="endereco_envio_linha1", type="string", example="Rua A, 123"),
     *                 @OA\Property(property="endereco_envio_linha2", type="string", example="Apt 101"),
     *                 @OA\Property(property="endereco_envio_linha3", type="string", example="Bloco B"),
     *                 @OA\Property(property="cidade_envio", type="string", example="São Paulo"),
     *                 @OA\Property(property="estado_envio", type="string", example="SP"),
     *                 @OA\Property(property="codigo_postal_envio", type="string", example="12345-678"),
     *                 @OA\Property(property="pais_envio", type="string", example="Brasil"),
     *                 @OA\Property(property="id_cliente", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string"),
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Endereco::all();
    }

    /**
     * @OA\Post(
     *     path="/api/enderecos",
     *     summary="Criar um novo endereço",
     *     tags={"Enderecos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nivel_servico_envio", type="string", example="Express"),
     *             @OA\Property(property="endereco_envio_linha1", type="string", example="Rua A, 123"),
     *             @OA\Property(property="endereco_envio_linha2", type="string", example="Apt 101"),
     *             @OA\Property(property="endereco_envio_linha3", type="string", example="Bloco B"),
     *             @OA\Property(property="cidade_envio", type="string", example="São Paulo"),
     *             @OA\Property(property="estado_envio", type="string", example="SP"),
     *             @OA\Property(property="codigo_postal_envio", type="string", example="12345-678"),
     *             @OA\Property(property="pais_envio", type="string", example="Brasil"),
     *             @OA\Property(property="id_cliente", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Endereço criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nivel_servico_envio", type="string", example="Express"),
     *             @OA\Property(property="endereco_envio_linha1", type="string", example="Rua A, 123"),
     *             @OA\Property(property="endereco_envio_linha2", type="string", example="Apt 101"),
     *             @OA\Property(property="endereco_envio_linha3", type="string", example="Bloco B"),
     *             @OA\Property(property="cidade_envio", type="string", example="São Paulo"),
     *             @OA\Property(property="estado_envio", type="string", example="SP"),
     *             @OA\Property(property="codigo_postal_envio", type="string", example="12345-678"),
     *             @OA\Property(property="pais_envio", type="string", example="Brasil"),
     *             @OA\Property(property="id_cliente", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $endereco = Endereco::create($request->all());
        return response()->json($endereco, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/enderecos/{id}",
     *     summary="Buscar endereço pelo ID",
     *     tags={"Enderecos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do endereço",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nivel_servico_envio", type="string", example="Express"),
     *             @OA\Property(property="endereco_envio_linha1", type="string", example="Rua A, 123"),
     *             @OA\Property(property="endereco_envio_linha2", type="string", example="Apt 101"),
     *             @OA\Property(property="endereco_envio_linha3", type="string", example="Bloco B"),
     *             @OA\Property(property="cidade_envio", type="string", example="São Paulo"),
     *             @OA\Property(property="estado_envio", type="string", example="SP"),
     *             @OA\Property(property="codigo_postal_envio", type="string", example="12345-678"),
     *             @OA\Property(property="pais_envio", type="string", example="Brasil"),
     *             @OA\Property(property="id_cliente", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Endereço não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        return Endereco::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/enderecos/{id}",
     *     summary="Atualizar um endereço pelo ID",
     *     tags={"Enderecos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do endereço",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nivel_servico_envio", type="string", example="Express"),
     *             @OA\Property(property="endereco_envio_linha1", type="string", example="Rua A, 123"),
     *             @OA\Property(property="endereco_envio_linha2", type="string", example="Apt 101"),
     *             @OA\Property(property="endereco_envio_linha3", type="string", example="Bloco B"),
     *             @OA\Property(property="cidade_envio", type="string", example="São Paulo"),
     *             @OA\Property(property="estado_envio", type="string", example="SP"),
     *             @OA\Property(property="codigo_postal_envio", type="string", example="12345-678"),
     *             @OA\Property(property="pais_envio", type="string", example="Brasil"),
     *             @OA\Property(property="id_cliente", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nivel_servico_envio", type="string", example="Express"),
     *             @OA\Property(property="endereco_envio_linha1", type="string", example="Rua A, 123"),
     *             @OA\Property(property="endereco_envio_linha2", type="string", example="Apt 101"),
     *             @OA\Property(property="endereco_envio_linha3", type="string", example="Bloco B"),
     *             @OA\Property(property="cidade_envio", type="string", example="São Paulo"),
     *             @OA\Property(property="estado_envio", type="string", example="SP"),
     *             @OA\Property(property="codigo_postal_envio", type="string", example="12345-678"),
     *             @OA\Property(property="pais_envio", type="string", example="Brasil"),
     *             @OA\Property(property="id_cliente", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Endereço não encontrado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $endereco = Endereco::findOrFail($id);
        $endereco->update($request->all());
        return response()->json($endereco, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/enderecos/{id}",
     *     summary="Deletar um endereço pelo ID",
     *     tags={"Enderecos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do endereço",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Endereço deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Endereço não encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $endereco = Endereco::findOrFail($id);
        $endereco->delete();
        return response()->json(null, 204);
    }
}
