<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Respuesta de éxito.
     */
    protected function successResponse($data = null, string $message = 'Operación exitosa.', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta de error general.
     */
    protected function errorResponse(string $message = 'Ocurrió un error.', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta de validación fallida (422).
     */
    protected function validationErrorResponse($errors, string $message = 'Los datos proporcionados no son válidos.'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Respuesta de no autenticado (401).
     */
    protected function unauthorizedResponse(string $message = 'No autenticado.'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Respuesta de no autorizado (403).
     */
    protected function forbiddenResponse(string $message = 'No tienes permiso para realizar esta acción.'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Respuesta de no encontrado (404).
     */
    protected function notFoundResponse(string $message = 'Recurso no encontrado.'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }
}
