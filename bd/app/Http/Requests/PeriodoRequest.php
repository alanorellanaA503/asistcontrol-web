<?php

namespace App\Http\Requests;

use App\Models\Periodo;
use Illuminate\Contracts\Validation\Validator;

class PeriodoRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'nombre' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string', 'max:255'],
            'fecha_inicio' => [$this->isMethod('post') ? 'required' : 'sometimes', 'date'],
            'fecha_fin' => [$this->isMethod('post') ? 'required' : 'sometimes', 'date'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('fecha_inicio') || $validator->errors()->has('fecha_fin')) {
                return;
            }

            $periodo = $this->route('periodo');
            $inicio = $this->input('fecha_inicio', $periodo instanceof Periodo ? $periodo->fecha_inicio : null);
            $fin = $this->input('fecha_fin', $periodo instanceof Periodo ? $periodo->fecha_fin : null);

            if ($inicio && $fin && strtotime($fin) < strtotime($inicio)) {
                $validator->errors()->add('fecha_fin', 'La fecha fin debe ser igual o posterior a la fecha inicio.');
            }
        }];
    }
}
