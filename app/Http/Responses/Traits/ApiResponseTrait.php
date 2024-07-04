<?php

namespace App\Http\Responses\Traits;

trait ApiResponseTrait
{
    protected function successResponse($data, $message = 'Success', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse($message = 'Error', $status = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null
        ], $status);
    }
}
