<?php

namespace App\Http\Requests;

use App\Models\Curso;
use Illuminate\Contracts\Validation\Validator;

class CursoRequest extends ApiRequest
{
    public function rules(): array
    {
        $curso = $this->route('curso');
        return [
            'id_profesor' => ['bail', $this->isMethod('post') ? 'required' : 'sometimes', 'integer', 'exists:profesor,id_profesor'],
            'id_periodo' => ['bail', $this->isMethod('post') ? 'required' : 'sometimes', 'integer', 'exists:periodo,id_periodo'],
            'grupo' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string', 'max:100'],
            'aula' => ['sometimes', 'nullable', 'string', 'max:100'],
            'id_materia' => ['bail', $this->isMethod('post') ? 'required' : 'sometimes', 'integer', 'exists:materia,id_materia'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $curso = $this->route('curso');
            $idMateria = $this->input('id_materia', $curso instanceof Curso ? $curso->id_materia : null);
            $idPeriodo = $this->input('id_periodo', $curso instanceof Curso ? $curso->id_periodo : null);
            $grupo = $this->input('grupo', $curso instanceof Curso ? $curso->grupo : null);

            if (! $idMateria || ! $idPeriodo || ! $grupo) {
                return;
            }

            $duplicado = Curso::where('id_materia', $idMateria)
                ->where('id_periodo', $idPeriodo)
                ->where('grupo', $grupo)
                ->when($curso instanceof Curso, fn ($query) => $query->where('id_curso', '!=', $curso->id_curso))
                ->exists();

            if ($duplicado) {
                $validator->errors()->add('grupo', 'Ya existe un curso con esa materia, ciclo y grupo.');
            }
        }];
    }
}
