<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatriculaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_matricula' => $this->id_matricula,
            'id_estudiante' => $this->id_estudiante,
            'id_curso' => $this->id_curso,
            'estado' => $this->estado,
            'estudiante' => $this->whenLoaded('estudiante', fn () => [
                'id_estudiante' => $this->estudiante->id_estudiante,
                'carnet' => $this->estudiante->carnet,
                'usuario' => [
                    'id_usuario' => $this->estudiante->usuario->id_usuario,
                    'nombre' => $this->estudiante->usuario->nombre,
                    'apellido' => $this->estudiante->usuario->apellido,
                ],
            ]),
            'curso' => $this->whenLoaded('curso', fn () => new CursoResource($this->curso)),
        ];
    }
}
