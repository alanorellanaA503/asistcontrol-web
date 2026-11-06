<?php

namespace App\Http\Requests;

use App\Models\Curso;
use App\Models\SesionClase;
use Illuminate\Contracts\Validation\Validator;

class SesionClaseRequest extends ApiRequest
{
    public function rules(): array
    {
        $curso = $this->route('curso');

        return [
            'fecha' => [
                'bail',
                'required',
                'date',
            ],
            'tema' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('fecha')) {
                return;
            }

            $curso = $this->route('curso');

            if ($curso instanceof Curso && SesionClase::where('id_curso', $curso->id_curso)
                ->whereDate('fecha', $this->input('fecha'))
                ->exists()) {
                $validator->errors()->add('fecha', 'Ya existe una sesión para este curso en esa fecha.');
            }
        }];
    }
}
