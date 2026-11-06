<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JustificacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_justificacion' => $this->id_justificacion,
            'id_asistencia' => $this->id_asistencia,
            'id_admin' => $this->id_admin,
            'motivo' => $this->motivo,
            'estado' => $this->estado,
            'creada_en' => $this->creada_en,
            'asistencia' => $this->whenLoaded('asistencia', fn () => new AsistenciaResource($this->asistencia)),
        ];
    }
}
