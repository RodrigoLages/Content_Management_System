<?php

namespace App\Http\Responses;

use App\Http\Responses\Traits\ApiResponseTrait;

class PostResponse
{
    use ApiResponseTrait;

    public function success($post, $message = 'Post retrieved successfully', $status = 200)
    {
        return $this->successResponse($post, $message, $status);
    }

    public function notFound($message = 'Post not found', $status = 404)
    {
        return $this->errorResponse($message, $status);
    }

    // Adicione outros métodos específicos de resposta conforme necessário
}
