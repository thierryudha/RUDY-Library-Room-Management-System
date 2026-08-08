<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Build a success response
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    protected function successResponse(string $message = 'Success', $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Build an error response
     *
     * @param  string  $message
     * @param  int  $code
     * @param  mixed  $data
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Build a validation error response
     *
     * @param  mixed  $errors
     * @param  string  $message
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors, string $message = 'Validation Error'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $errors,
        ], 422);
    }

    /**
     * Build a not found response
     *
     * @param  string  $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
        ], 404);
    }
}
