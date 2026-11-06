<?php

namespace App\Http\Requests;

use App\Models\Matricula;
use Illuminate\Contracts\Validation\Validator;

class MatriculaRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id_estudiante' => [
                'bail',
                'required',
                'integer',
                'exists:estudiante,id_estudiante',
            ],
            'id_curso' => ['bail', 'required', 'integer', 'exists:curso,id_curso'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (Matricula::where('id_estudiante', $this->input('id_estudiante'))
                ->where('id_curso', $this->input('id_curso'))
                ->exists()) {
                $validator->errors()->add('id_estudiante', 'El estudiante ya está matriculado en este curso.');
            }
        }];
    }
}
