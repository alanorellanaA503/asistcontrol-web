<?php

namespace App\Http\Requests;

class CrearJustificacionRequest extends ApiRequest
{
    public function rules(): array
    {
        return ['motivo' => ['required', 'string', 'max:5000']];
    }
}
