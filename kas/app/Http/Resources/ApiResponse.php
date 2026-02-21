<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Response Formatter
 * Gunakan class ini untuk format response API yang konsisten
 */
class ApiResponse extends JsonResource
{
    public static function success($data = null, $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error($message = 'Error', $errors = null, $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    public static function paginated($items, $message = 'Data retrieved successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ]
        ]);
    }

    public static function created($data, $message = 'Resource created successfully', $statusCode = 201)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function notFound($message = 'Resource not found')
    {
        return self::error($message, null, 404);
    }

    public static function unauthorized($message = 'Unauthorized')
    {
        return self::error($message, null, 401);
    }

    public static function forbidden($message = 'Forbidden')
    {
        return self::error($message, null, 403);
    }

    public static function validationError($message = 'Validation failed', $errors = [])
    {
        return self::error($message, $errors, 422);
    }
}
