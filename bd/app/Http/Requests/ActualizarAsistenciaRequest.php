<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ActualizarAsistenciaRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::in(['PRESENTE', 'AUSENTE', 'TARDE', 'JUSTIFICADO'])],
            'hora_registro' => ['nullable', 'date_format:H:i'],
            'observacion' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
