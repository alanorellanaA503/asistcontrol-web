<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfesorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_profesor' => $this->id_profesor,
            'id_usuario' => $this->id_usuario,
            'especialidad' => $this->especialidad,
            'telefono' => $this->telefono,
            'usuario' => $this->whenLoaded('usuario', fn () => [
                'id_usuario' => $this->usuario->id_usuario,
                'nombre' => $this->usuario->nombre,
                'apellido' => $this->usuario->apellido,
                'correo' => $this->usuario->correo,
            ]),
        ];
    }
}
