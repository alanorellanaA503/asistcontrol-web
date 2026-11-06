<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CrearJustificacionRequest;
use App\Http\Requests\EstudianteAsistenciasRequest;
use App\Http\Resources\AsistenciaResource;
use App\Http\Resources\CursoResource;
use App\Http\Resources\JustificacionResource;
use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Justificacion;
use App\Models\Matricula;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EstudianteController extends Controller
{
    public function cursos(Request $request)
    {
        $estudiante = $this->estudianteFor($request);

        $matriculas = $estudiante->matriculas()->with(['curso.materia', 'curso.profesor.usuario', 'curso.periodo'])
            ->where('estado', 'ACTIVA')
            ->orderBy('id_matricula')
            ->paginate(15);

        return CursoResource::collection(
            $matriculas->through(fn (Matricula $matricula) => $matricula->curso)
        );
    }

    public function asistencias(EstudianteAsistenciasRequest $request)
    {
        $estudiante = $this->estudianteFor($request);
        $query = Asistencia::with(['sesion.curso.materia', 'matricula.curso'])
            ->whereHas('matricula', fn ($query) => $query->where('id_estudiante', $estudiante->id_estudiante));

        if ($request->validated('curso')) {
            $query->whereHas('matricula', fn ($query) => $query->where('id_curso', $request->validated('curso')));
        }

        return AsistenciaResource::collection($query->orderByDesc('id_asistencia')->paginate(15));
    }

    public function porcentaje(Request $request, Curso $curso): JsonResponse
    {
        $estudiante = $this->estudianteFor($request);
        $matricula = Matricula::where('id_estudiante', $estudiante->id_estudiante)
            ->where('id_curso', $curso->id_curso)
            ->where('estado', 'ACTIVA')
            ->firstOrFail();

        $totalSesiones = $curso->sesiones()->count();
        $asistenciasContadas = Asistencia::where('id_matricula', $matricula->id_matricula)
            ->whereIn('estado', ['PRESENTE', 'TARDE', 'JUSTIFICADO'])
            ->count();

        return response()->json([
            'id_curso' => $curso->id_curso,
            'total_sesiones' => $totalSesiones,
            'sesiones_asistidas' => $asistenciasContadas,
            'porcentaje' => $totalSesiones === 0 ? 0 : round(($asistenciasContadas / $totalSesiones) * 100, 2),
        ]);
    }

    public function justificar(CrearJustificacionRequest $request, Asistencia $asistencia): JsonResponse
    {
        $estudiante = $this->estudianteFor($request);
        $justificacion = DB::transaction(function () use ($request, $asistencia, $estudiante) {
            $asistencia = Asistencia::whereKey($asistencia->getKey())->lockForUpdate()->firstOrFail();
            $asistencia->load('matricula');

            abort_unless(
                $asistencia->matricula->id_estudiante === $estudiante->id_estudiante,
                403,
                'No puedes justificar la asistencia de otro estudiante.'
            );

            if ($asistencia->estado !== 'AUSENTE') {
                throw ValidationException::withMessages([
                    'asistencia' => ['Solo se pueden justificar asistencias en estado AUSENTE.'],
                ]);
            }

            if ($asistencia->justificacion()->exists()) {
                throw ValidationException::withMessages([
                    'asistencia' => ['Esta asistencia ya tiene una justificación registrada.'],
                ]);
            }

            return Justificacion::create([
                'id_asistencia' => $asistencia->id_asistencia,
                'motivo' => $request->validated('motivo'),
                'estado' => 'PENDIENTE',
            ]);
        });

        return (new JustificacionResource($justificacion))->response()->setStatusCode(201);
    }

    private function estudianteFor(Request $request): Estudiante
    {
        return Estudiante::where('id_usuario', $request->user()->id_usuario)->firstOrFail();
    }
}
