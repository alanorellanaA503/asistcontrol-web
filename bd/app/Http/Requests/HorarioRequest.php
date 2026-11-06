<?php

namespace App\Http\Requests;

use App\Models\Horario;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class HorarioRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id_curso' => ['bail', $this->isMethod('post') ? 'required' : 'sometimes', 'integer', 'exists:curso,id_curso'],
            'dia_semana' => [$this->isMethod('post') ? 'required' : 'sometimes', Rule::in(['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'])],
            'hora_inicio' => [$this->isMethod('post') ? 'required' : 'sometimes', 'date_format:H:i'],
            'hora_fin' => [$this->isMethod('post') ? 'required' : 'sometimes', 'date_format:H:i'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('hora_inicio') || $validator->errors()->has('hora_fin')) {
                return;
            }

            $horario = $this->route('horario');
            $inicio = $this->input('hora_inicio', $horario instanceof Horario ? $horario->hora_inicio : null);
            $fin = $this->input('hora_fin', $horario instanceof Horario ? $horario->hora_fin : null);

            if ($inicio && $fin && strtotime($fin) <= strtotime($inicio)) {
                $validator->errors()->add('hora_fin', 'La hora fin debe ser posterior a la hora inicio.');
            }
        }];
    }
}
