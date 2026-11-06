<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstudianteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_estudiante' => $this->id_estudiante,
            'carnet' => $this->carnet,
            'grado' => $this->grado,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'usuario' => [
                'id_usuario' => $this->usuario->id_usuario,
                'nombre' => $this->usuario->nombre,
                'apellido' => $this->usuario->apellido,
                'correo' => $this->usuario->correo,
            ],
        ];
    }
}
