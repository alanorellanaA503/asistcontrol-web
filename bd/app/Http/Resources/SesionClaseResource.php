<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SesionClaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_sesion' => $this->id_sesion,
            'id_curso' => $this->id_curso,
            'fecha' => $this->fecha,
            'tema' => $this->tema,
        ];
    }
}
