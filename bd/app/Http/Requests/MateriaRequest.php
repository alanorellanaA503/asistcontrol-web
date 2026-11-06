<?php

namespace App\Http\Requests;

class MateriaRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'nombre' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string', 'max:255'],
        ];
    }
}
