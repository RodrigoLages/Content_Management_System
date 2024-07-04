<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\PostRequestTrait;

class UpdatePostRequest extends FormRequest
{
    use PostRequestTrait;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return $this->rulesForUpdate();
    }
}
