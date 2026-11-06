<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistroAsistenciasRequest;
use App\Http\Requests\SesionClaseRequest;
use App\Http\Resources\AsistenciaResource;
use App\Http\Resources\SesionClaseResource;
use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\SesionClase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SesionClaseController extends Controller
{
    public function store(SesionClaseRequest $request, Curso $curso): \Illuminate\Http\JsonResponse
    {
        if ($request->user()->rol === 'ADMINISTRADOR') {
            abort_unless($curso->profesor()->exists(), 422, 'Asigna un profesor al curso antes de crear sesiones.');
            $profesor = $curso->profesor;
        } else {
            $profesor = ProfesorController::profesorFor($request);
            ProfesorController::assertOwnsCourse($profesor, $curso);
        }

        $sesion = SesionClase::create($request->validated() + ['id_curso' => $curso->id_curso]);

        return (new SesionClaseResource($sesion))->response()->setStatusCode(201);
    }

    public function registrarAsistencias(RegistroAsistenciasRequest $request, SesionClase $sesion)
    {
        $sesion->load('curso');
        if ($request->user()->rol === 'ADMINISTRADOR') {
            abort_unless($sesion->curso->profesor()->exists(), 422, 'El curso no tiene un profesor asignado.');
            $profesor = $sesion->curso->profesor;
        } else {
            $profesor = ProfesorController::profesorFor($request);
            ProfesorController::assertOwnsCourse($profesor, $sesion->curso);
        }

        $asistencias = DB::transaction(function () use ($request, $sesion, $profesor) {
            $records = [];

            foreach ($request->validated('asistencias') as $index => $item) {
                $matricula = Matricula::whereKey($item['id_matricula'])
                    ->where('id_curso', $sesion->id_curso)
                    ->where('estado', 'ACTIVA')
                    ->first();

                if (! $matricula) {
                    throw ValidationException::withMessages([
                        "asistencias.$index.id_matricula" => ['La matrícula debe estar activa y pertenecer al curso de la sesión.'],
                    ]);
                }

                if (Asistencia::where('id_sesion', $sesion->id_sesion)
                    ->where('id_matricula', $matricula->id_matricula)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        "asistencias.$index.id_matricula" => ['Ya existe asistencia para esta matrícula en la sesión.'],
                    ]);
                }

                $records[] = Asistencia::create([
                    'id_sesion' => $sesion->id_sesion,
                    'id_matricula' => $matricula->id_matricula,
                    'id_profesor' => $profesor->id_profesor,
                    'estado' => $item['estado'],
                    'hora_registro' => $item['hora_registro'] ?? null,
                    'observacion' => $item['observacion'] ?? null,
                ]);
            }

            return $records;
        });

        return AsistenciaResource::collection(collect($asistencias))
            ->response()->setStatusCode(201);
    }
}
