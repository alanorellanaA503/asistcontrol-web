<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DecisionJustificacionRequest;
use App\Http\Requests\JustificacionesIndexRequest;
use App\Http\Resources\JustificacionResource;
use App\Models\Administrador;
use App\Models\Asistencia;
use App\Models\Justificacion;
use Illuminate\Support\Facades\DB;

class JustificacionController extends Controller
{
    public function index(JustificacionesIndexRequest $request)
    {
        $query = Justificacion::with([
            'asistencia.sesion.curso.materia',
            'asistencia.matricula.estudiante.usuario',
        ])->orderByDesc('creada_en');

        if ($request->validated('estado')) {
            $query->where('estado', $request->validated('estado'));
        }

        return JustificacionResource::collection($query->paginate(15));
    }

    public function revisar(DecisionJustificacionRequest $request, Justificacion $justificacion): JustificacionResource
    {
        $decision = $request->validated('decision');
        $administrador = Administrador::where('id_usuario', $request->user()->id_usuario)->firstOrFail();

        $justificacion = DB::transaction(function () use ($decision, $justificacion, $administrador) {
            $justificacion = Justificacion::whereKey($justificacion->getKey())->lockForUpdate()->firstOrFail();

            if ($justificacion->estado !== 'PENDIENTE') {
                abort(422, 'La justificación ya fue revisada.');
            }

            $justificacion->update([
                'estado' => $decision,
                'id_admin' => $administrador->id_admin,
            ]);

            if ($decision === 'APROBADA') {
                Asistencia::whereKey($justificacion->id_asistencia)->update(['estado' => 'JUSTIFICADO']);
            }

            return $justificacion->load([
                'asistencia.sesion.curso.materia',
                'asistencia.matricula.estudiante.usuario',
            ]);
        });

        return new JustificacionResource($justificacion);
    }
}
