<?php

namespace App\Http\Requests\Traits;

trait PostRequestTrait
{
    public function rulesForCreate()
    {
        return [
            'title' => 'required|max:255',
            'author' => 'required|max:100',
            'content' => 'required',
            'tags' => 'required|array',
            'tags.*' => 'string|max:30'
        ];

    }

    public function rulesForUpdate()
    {
        return [
            'title' => 'sometimes|required|max:255',
            'author' => 'sometimes|required|max:100',
            'content' => 'sometimes|required',
            'tags.*' => 'string|max:30'
        ];
    }
}
