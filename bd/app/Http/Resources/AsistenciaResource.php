<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsistenciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_asistencia' => $this->id_asistencia,
            'id_sesion' => $this->id_sesion,
            'id_matricula' => $this->id_matricula,
            'id_profesor' => $this->id_profesor,
            'estado' => $this->estado,
            'hora_registro' => $this->hora_registro,
            'observacion' => $this->observacion,
            'sesion' => $this->whenLoaded('sesion', fn () => new SesionClaseResource($this->sesion)),
            'matricula' => $this->whenLoaded('matricula', fn () => new MatriculaResource($this->matricula)),
        ];
    }
}
