<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class DecisionJustificacionRequest extends ApiRequest
{
    public function rules(): array
    {
        return ['decision' => ['required', Rule::in(['APROBADA', 'RECHAZADA'])]];
    }
}
