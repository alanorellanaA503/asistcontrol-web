<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CursoResource;
use App\Http\Resources\MatriculaResource;
use App\Http\Resources\ProfesorResource;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Profesor;
use Illuminate\Http\Request;

class ProfesorController extends Controller
{
    public function index()
    {
        return ProfesorResource::collection(
            Profesor::with('usuario')->orderBy('id_profesor')->paginate(15)
        );
    }

    public function cursos(Request $request)
    {
        $cursos = $request->user()->rol === 'ADMINISTRADOR'
            ? Curso::query()
            : $this->profesorFor($request)->cursos();

        return CursoResource::collection($cursos->with(['materia', 'profesor.usuario', 'periodo'])
            ->orderBy('id_curso')->paginate(15));
    }

    public function estudiantes(Request $request, Curso $curso)
    {
        if ($request->user()->rol !== 'ADMINISTRADOR') {
            $profesor = $this->profesorFor($request);
            $this->assertOwnsCourse($profesor, $curso);
        }

        $matriculas = Matricula::with('estudiante.usuario')
            ->where('id_curso', $curso->id_curso)
            ->where('estado', 'ACTIVA')
            ->orderBy('id_matricula')
            ->paginate(15);

        return MatriculaResource::collection($matriculas);
    }

    public static function assertOwnsCourse(Profesor $profesor, Curso $curso): void
    {
        abort_unless($curso->id_profesor === $profesor->id_profesor, 403, 'No tienes permiso para acceder a este curso.');
    }

    public static function profesorFor(Request $request): Profesor
    {
        return Profesor::where('id_usuario', $request->user()->id_usuario)->firstOrFail();
    }
}
