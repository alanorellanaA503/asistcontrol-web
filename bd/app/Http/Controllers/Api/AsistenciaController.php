<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarAsistenciaRequest;
use App\Http\Resources\AsistenciaResource;
use App\Models\Asistencia;

class AsistenciaController extends Controller
{
    public function update(ActualizarAsistenciaRequest $request, Asistencia $asistencia): AsistenciaResource
    {
        $asistencia->load('sesion.curso');
        if ($request->user()->rol !== 'ADMINISTRADOR') {
            $profesor = ProfesorController::profesorFor($request);
            ProfesorController::assertOwnsCourse($profesor, $asistencia->sesion->curso);
        }

        $asistencia->update($request->validated());

        return new AsistenciaResource($asistencia->refresh()->load(['sesion', 'matricula']));
    }
}
