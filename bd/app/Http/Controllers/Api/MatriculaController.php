<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatriculaRequest;
use App\Http\Resources\MatriculaResource;
use App\Models\Matricula;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MatriculaController extends Controller
{
    public function store(MatriculaRequest $request): JsonResponse
    {
        $matricula = Matricula::create($request->validated() + ['estado' => 'ACTIVA'])
            ->load(['estudiante.usuario', 'curso']);

        return (new MatriculaResource($matricula))->response()->setStatusCode(201);
    }

    public function retirar(Matricula $matricula): MatriculaResource
    {
        if ($matricula->estado !== 'ACTIVA') {
            throw ValidationException::withMessages([
                'matricula' => ['La matrícula ya está retirada.'],
            ]);
        }

        $matricula->update(['estado' => 'RETIRADA']);

        return new MatriculaResource($matricula->refresh()->load(['estudiante.usuario', 'curso']));
    }
}
