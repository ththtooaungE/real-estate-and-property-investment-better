<?php

namespace App\Traits;

trait ApiResponseFormatter
{
    public function successResponse($status, $message, $statusCode, $data = [])
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function paginatedSuccessResponse($status, $message, $statusCode, $data)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'path' => $data->url($data->currentPage()),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'links' => [
                    'first' => $data->url(1),
                    'last' => $data->url($data->lastPage()),
                    'prev' => $data->previousPageUrl(),
                    'next' => $data->nextPageUrl()
                ]
            ]
        ], $statusCode);
    }

    public function errorResponse($status, $message, $statusCode)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $statusCode);
    }
}
