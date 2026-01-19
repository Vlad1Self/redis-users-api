<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = [], mixed $meta = [], string $message = 'Успешно'): JsonResponse
    {
        return new JsonResponse([
            'data' => $data,
            'meta' => $meta,
            'status' => [
                'code' => 200,
                'message' => $message,
                'description' => 'Успешно'
            ]
        ], 200);
    }

    public static function error(int $statusCode, string $message, ?string $description = null, mixed $data = []): JsonResponse
    {
        return new JsonResponse([
            'data' => $data,
            'meta' => [],
            'status' => [
                'code' => $statusCode,
                'message' => $message,
                'description' => $description ?? $message,
            ]
        ], $statusCode);
    }
}
