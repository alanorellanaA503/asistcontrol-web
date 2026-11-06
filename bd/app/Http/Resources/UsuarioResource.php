<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_usuario' => $this->id_usuario,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'correo' => $this->correo,
            'rol' => $this->rol,
            'activo' => $this->activo,
            'debe_cambiar_password' => $this->rol !== 'ADMINISTRADOR' && (bool) $this->debe_cambiar_password,
            'creado_en' => $this->creado_en,
            'administrador' => $this->whenLoaded('administrador'),
            'profesor' => $this->whenLoaded('profesor'),
            'estudiante' => $this->whenLoaded('estudiante'),
        ];
    }
}
