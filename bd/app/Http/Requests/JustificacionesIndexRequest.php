<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class JustificacionesIndexRequest extends ApiRequest
{
    public function rules(): array
    {
        return ['estado' => ['sometimes', Rule::in(['PENDIENTE', 'APROBADA', 'RECHAZADA'])]];
    }
}
