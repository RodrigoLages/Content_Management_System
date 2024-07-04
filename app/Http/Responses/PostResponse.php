<?php

namespace App\Http\Responses;

use App\Http\Responses\Traits\ApiResponseTrait;

class PostResponse
{
    use ApiResponseTrait;

    public function success($post, $status = 200)
    {
        return $this->successResponse($post, $status);
    }

    public function notFound($message = 'Post not found', $status = 404)
    {
        return $this->errorResponse($message, $status);
    }

    // Adicione outros métodos específicos de resposta conforme necessário
}
