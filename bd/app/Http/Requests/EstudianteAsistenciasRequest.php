<?php

namespace App\Http\Requests;

class EstudianteAsistenciasRequest extends ApiRequest
{
    public function rules(): array
    {
        return ['curso' => ['bail', 'sometimes', 'integer', 'exists:curso,id_curso']];
    }
}
