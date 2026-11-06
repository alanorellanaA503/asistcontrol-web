<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RegistroAsistenciasRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'asistencias' => ['required', 'array', 'min:1'],
            'asistencias.*.id_matricula' => ['bail', 'required', 'integer', 'distinct', 'exists:matricula,id_matricula'],
            'asistencias.*.estado' => ['required', Rule::in(['PRESENTE', 'AUSENTE', 'TARDE', 'JUSTIFICADO'])],
            'asistencias.*.hora_registro' => ['nullable', 'date_format:H:i'],
            'asistencias.*.observacion' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
