<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CursoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_curso' => $this->id_curso,
            'id_materia' => $this->id_materia,
            'id_profesor' => $this->id_profesor,
            'id_periodo' => $this->id_periodo,
            'grupo' => $this->grupo,
            'aula' => $this->aula,
            'materia' => $this->whenLoaded('materia', fn () => new MateriaResource($this->materia)),
            'profesor' => $this->whenLoaded('profesor', fn () => [
                'id_profesor' => $this->profesor->id_profesor,
                'usuario' => [
                    'id_usuario' => $this->profesor->usuario->id_usuario,
                    'nombre' => $this->profesor->usuario->nombre,
                    'apellido' => $this->profesor->usuario->apellido,
                ],
            ]),
            'ciclo' => $this->whenLoaded('periodo', fn () => new PeriodoResource($this->periodo)),
        ];
    }
}
