<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeriodoRequest;
use App\Http\Resources\PeriodoResource;
use App\Models\Periodo;
use Illuminate\Http\JsonResponse;

class PeriodoController extends Controller
{
    public function index()
    {
        return PeriodoResource::collection(Periodo::orderBy('id_periodo')->paginate(15));
    }

    public function store(PeriodoRequest $request): JsonResponse
    {
        return (new PeriodoResource(Periodo::create($request->validated())))
            ->response()->setStatusCode(201);
    }

    public function show(Periodo $periodo): PeriodoResource
    {
        return new PeriodoResource($periodo);
    }

    public function update(PeriodoRequest $request, Periodo $periodo): PeriodoResource
    {
        $periodo->update($request->validated());

        return new PeriodoResource($periodo->refresh());
    }

    public function destroy(Periodo $periodo): JsonResponse
    {
        $periodo->delete();

        return response()->json(['message' => 'Ciclo eliminado correctamente.']);
    }
}
