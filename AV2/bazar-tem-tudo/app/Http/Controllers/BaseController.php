<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * @OA\Info(
 *    title="APIs do BazarTemTudo",
 *    version="1.0.0",
 * )
 */
class BaseController extends Controller
{
    /**
     * Responde com sucesso (status 200) e mensagem padrão.
     *
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithSuccess($data = null)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Responde com erro (status 400) e mensagem de erro.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message, $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'error' => $message,
        ], $statusCode);
    }
}
