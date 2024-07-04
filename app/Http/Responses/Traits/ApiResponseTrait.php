<?php

namespace App\Http\Responses\Traits;

trait ApiResponseTrait
{
    protected function successResponse($data, $status = 200)
    {
        return response()->json($data, $status);
    }

    protected function errorResponse($message = 'Error', $status = 400)
    {
        return response()->json([
            'message' => $message,
            'data' => null
        ], $status);
    }
}
