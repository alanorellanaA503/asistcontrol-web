<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HorarioRequest;
use App\Http\Resources\HorarioResource;
use App\Models\Horario;
use Illuminate\Http\JsonResponse;

class HorarioController extends Controller
{
    public function index()
    {
        return HorarioResource::collection(Horario::orderBy('id_horario')->paginate(15));
    }

    public function store(HorarioRequest $request): JsonResponse
    {
        return (new HorarioResource(Horario::create($request->validated())))
            ->response()->setStatusCode(201);
    }

    public function show(Horario $horario): HorarioResource
    {
        return new HorarioResource($horario);
    }

    public function update(HorarioRequest $request, Horario $horario): HorarioResource
    {
        $horario->update($request->validated());

        return new HorarioResource($horario->refresh());
    }

    public function destroy(Horario $horario): JsonResponse
    {
        $horario->delete();

        return response()->json(['message' => 'Horario eliminado correctamente.']);
    }
}
